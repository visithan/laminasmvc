<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Quiz\AnswerForm;
use Application\Form\Quiz\CreateForm;
use Application\Form\Quiz\DeleteForm;
use Application\Model\Table\AnswersTable;
use Application\Model\Table\QuizzesTable;
use Application\Model\Table\TalliesTable;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class QuizController extends AbstractActionController
{
	private $answersTable;
	private $createForm;
	private $quizzesTable;
	private $talliesTable;

	public function __construct(
		AnswersTable $answersTable,
		CreateForm $createForm,
		QuizzesTable $quizzesTable,
		TalliesTable $talliesTable
	) 
	{
		$this->answersTable = $answersTable;
		$this->createForm   = $createForm;
		$this->quizzesTable = $quizzesTable;
		$this->talliesTable = $talliesTable;
	}

	public function answerAction()
	{
		$id = (int)$this->params()->fromRoute('id');
		if (empty($id) || !$id) {
			$this->notFoundAction();
		}
		
		
		$info = $this->quizzesTable->fetchQuizById((int) $id);
		if(!$info) {
		   return $this->notFoundAction();
		}

		if($info->getTimeout() < date('Y-m-d H:i:s')) {
			$this->quizzesTable->closeQuiz((int) $info->getQuizId());
			$this->redirect()->refresh();
		}

		if($info->getStatus() == 'Closed') {
			$this->flashMessenger()->addInfoMessage('This quiz is no longer active. You can only view its results');
			return $this->redirect()->toRoute('quiz', ['action' => 'view', 'id' => $info->getQuizId()]);
		}

		$answerForm = new AnswerForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$answerForm->setInputFilter($this->answersTable->getAnswerFormFilter());
			$answerForm->setData($formData);

			if($answerForm->isValid()) {
				try {

					$data = $answerForm->getData();
					$this->answersTable->updateAnswerTally((int) $data['answer_id'], (int) $info->getQuizId());
					$this->talliesTable->saveMyAnswer($data, (int) $info->getQuizId());
					$this->quizzesTable->updateTotal((int) $info->getQuizId());

					$this->flashMessenger()->addSuccessMessage('You have successfully saved your answer.');
					return $this->redirect()->toRoute('quiz', ['action' => 'view', 'id' => $info->getQuizId()]);
				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh();
				}
			}
		}

		return new ViewModel([
			'form' => $answerForm,
			'quiz' => $info,
			'record' => $this->talliesTable,
			'insight' => $this->answersTable
		]);
	}

	public function createAction()
	{
		# logged in users only please!
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}	

		$addForm = $this->createForm;
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$addForm->setInputFilter($this->quizzesTable->getCreateFormFilter());
			$addForm->setData($formData);


			if($addForm->isValid()) {
				try {

					$data = $addForm->getData();
					$info = $this->quizzesTable->saveQuiz($data);
					$id   = (int)$info->getGeneratedValue();  # get lastInsertId

					#sanitize answers[] field input
					$answers = (array) $this->params()->fromPost('answers');
					$answers = array_filter(array_map('strip_tags', $answers)); # strip html tags
					$answers = array_filter(array_map('trim', $answers));       # trim empty spaces
					$answers = array_slice($answers, 0, 5); # we restrict to 5 fields i meant

					foreach($answers as $index => $answer) {
						if(mb_strlen($answer) > 100) {
							$answers[$index] = mb_substr($answer, 0, 100);
						}

						$this->answersTable->saveAnswer($answer, (int) $id);
					}

					$this->flashMessenger()->addSuccessMessage('Quiz successfully posted.');
					return $this->redirect()->toRoute('quiz', ['action' => 'answer', 'id' => $id]);

				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh();
				}
			}
		}

		return new ViewModel(['form' => $addForm]);
	}

	public function deleteAction()
	{
		# allow only logged in users
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		$id = (int) $this->params()->fromRoute('id');
		if(!is_numeric($id)) {
			return $this->notFoundAction();
		}
 
		$info = $this->quizzesTable->fetchQuizById((int)$id);
		if(!$info) {
			return $this->notFoundAction();
		}

		$deleteForm = new DeleteForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$deleteForm->setData($formData);

			if($deleteForm->isValid()) {
				if($request->getPost()->get('delete_quiz') == 'Yes') {
					# now check that the person deleting the quiz is the author of the quiz
					if($info->getUserId() == $this->authPlugin()->getUserId()) {

						$this->quizzesTable->deleteQuizById((int)$info->getQuizId());
						$this->flashMessenger()->addInfoMessage('Quiz successfully deleted!');

						return $this->redirect()->toRoute('quiz', ['action' => 'index']);
					}

					# redirect this person away from this page with a warning
					$this->flashMessenger()->addWarningMessage('You can only delete quiz you have posted');
					return $this->redirect()->toRoute('home');
				}

				# here as well. The person presumably has clicked the No button
				return $this->redirect()->toRoute('quiz', ['action' => 'index']);
			}
		}

		return new ViewModel([
			'form' => $deleteForm,
			'quiz' => $info
		]);
	}

	public function indexAction()
	{
		# logged in user allowed here.
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		return new ViewModel([
			'quizzes' => $this->quizzesTable->fetchAllMyQuizzes((int)$this->authPlugin()->getUserId())
		]);
	}

	public function viewAction()
	{
		$id = (int)$this->params()->fromRoute('id');
		$info = $this->quizzesTable->fetchQuizById((int) $id);

		if(!is_numeric($id) || !$info) {
			return $this->notFoundAction();
		}

		$auth = new AuthenticationService();
		if(!$auth->hasIdentity() || $this->authPlugin()->getUserId() != $info->getUserId()) {
			$this->quizzesTable->updateViews((int) $info->getQuizId());
		}

		return new ViewModel([
			'quiz' => $info,
			'answerTable' => $this->answersTable
		]);
	}
}

