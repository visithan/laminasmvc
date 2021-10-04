<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Sendmail;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\Auth\ForgotForm;
use User\Form\Auth\ResetForm;
use User\Model\Table\ForgotTable;
use User\Model\Table\UsersTable;

class PasswordController extends AbstractActionController
{
	private $forgotTable;
	private $usersTable;

	public function __construct(
		ForgotTable $forgotTable,
		UsersTable $usersTable
	) {
		$this->forgotTable = $forgotTable;
		$this->usersTable = $usersTable;
	}

	public function forgotAction()
	{
		# makes sure those already logged in do not access this page
		$auth = new AuthenticationService();
		if($auth->hasIdentity()) {
			return $this->redirect()->toRoute();
		}

		$forgotForm = new ForgotForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$forgotForm->setInputFilter($this->usersTable->getForgotFormFilter());
			$forgotForm->setData($formData);

			if($forgotForm->isValid()) {
				try {
					$data = $forgotForm->getData();
					$info = $this->usersTable->fetchAccountByEmail($data['email']);
					$id   = (int) $info->getUserId();

					# remove any tokens that belong to the user who provided the email
					$this->forgotTable->deleteToken((int)$id);
					# now generate a new token
					$token = $this->forgotTable->generateToken(15);
					# next save the newly created token
					$this->forgotTable->saveToken((string) $token, (int) $id);

					#we want to fetch message from forgot.tpl file
					$file = dirname(dirname(dirname(__FILE__))) . DS . 'data' . DS . 'tpl' . DS . 'forgot.tpl';
					$file = file_get_contents($file);
					$body = str_replace('#USERNAME#', $info->getUsername(), $file);
					# this will yield something like http://www.laminas.edu/reset/5/utTbdTwXwiP
					# note we have not yet created the reset route. Let us do so now
					$link = $_SERVER['HTTP_HOST'] . '/reset/' . $id . '/' . $token;
					$body = str_replace('#LINK#', $link, $body);

					#var_dump($body); die(); #It should work

					
					# now send the message to the user's email address
					$message = new Message();
					$message->setFrom('admin@laminas.edu')
					        ->setTo($info->getEmail())
					        ->setSubject('I forgot my password')
					        ->setBody($body);

					if($message->isValid()) {
						(new Sendmail())->send($message);
					}

					$this->flashMessenger()->addSuccessMessage('Message successfully sent!');
					return $this->redirect()->toRoute('home');

				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh(); # refresh the page to show error
				}
			}
		}

		return (new ViewModel(['form' => $forgotForm]))->setTemplate('user/auth/forgot');
	}

	
	public function resetAction()
	{
		$auth = new AuthenticationService();
		if($auth->hasIdentity()) {
			return $this->redirect()->toRoute();
		}

		# our apps url will reach this method in the form domain/reset/id/token
		# so here we want to fetch the id and token part of the url
		$id = (int) $this->params()->fromRoute('id');
		$token = (string) $this->params()->fromRoute('token');
		$info = $this->usersTable->fetchAccountById((int)$id);

		if(empty($id) || empty($token) || !$info) {
			return $this->notFoundAction(); # will display a 404 error
		}

		# reaching here should mean we are good
		# so let us clear any old tokens in the forgot table
		$this->forgotTable->clearOldTokens();

		# here we want to check if the user had in fact requested to reset their password
		# through the forgot form.. remember that stage? 
		$verify = $this->forgotTable->fetchToken($token, (int)$info->getUserId());
		if(!$verify) {
			$this->flashMessenger()->addErrorMessage('Invalid token data. Try requesting for a token');
			return $this->redirect()->toRoute('forgot');
		}

		$resetForm = new ResetForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$resetForm->setInputFilter($this->usersTable->getResetFormFilter());
			$resetForm->setData($formData);

			if($resetForm->isValid()) {
				try {
					$data = $resetForm->getdata();
					if($this->usersTable->updatePassword($data['new_password'], (int) $info->getUserId())) {
						$this->forgotTable->deleteToken((int)$info->getUserId());
					}

					$this->flashMessenger()->addSuccessMessage('Password successfully reset. You can now login.');
					return $this->redirect()->toRoute('login');
				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh();
				}
			}
		}

		return (new ViewModel(['form' => $resetForm]))->setTemplate('user/auth/reset');
	}
}
