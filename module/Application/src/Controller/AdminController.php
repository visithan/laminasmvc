<?php


declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Table\QuizzesTable;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
	private $quizzesTable;

	public function __construct(QuizzesTable $quizzesTable)
	{
		$this->quizzesTable = $quizzesTable;
	}

	public function indexAction()
	{

		$auth = new AuthenticationService();
		if(!$auth->hasIdentity() || $this->identity()->role_id != 1) {
			return $this->redirect()->toRoute('login');
		}

		return new ViewModel([
			'quizzes' => $this->quizzesTable->fetchLatestQuizzes()
		]);
	}
}
