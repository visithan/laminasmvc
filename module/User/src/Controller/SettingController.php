<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\Setting\DeleteForm;
use User\Form\Setting\EmailForm;
use User\Form\Setting\PasswordForm;
use User\Form\Setting\UsernameForm;
use User\Model\Table\UsersTable;

class SettingController extends AbstractActionController
{
	private $usersTable;

	public function __construct(UsersTable $usersTable)
	{
		$this->usersTable = $usersTable;
	}

	public function deleteAction()
	{
		# make sure only signed in users see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		$deleteForm = new DeleteForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$deleteForm->setData($formData);

			if($deleteForm->isValid()) {
				if($request->getPost()->get('delete_account') == 'Yes') {
					$this->usersTable->deleteAccount((int) $this->authPlugin()->getUserId());
					$this->flashMessenger()->addSuccessMessage('Account successfully deleted.');
					# now clear all sessions that belongs to this user
					return $this->redirect()->toRoute('logout');
				}

				# otherwise return to the homepage
				return $this->redirect()->toRoute('home');
			}
		}


		return new ViewModel(['form' => $deleteForm]);
	}


	public function emailAction()
	{
		# only registered users should see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		$emailForm = new EmailForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$emailForm->setInputFilter($this->usersTable->getEmailFormFilter());
			$emailForm->setData($formData);

			if($emailForm->isValid()) {
				try {
					$data = $emailForm->getData();
					$this->usersTable->updateEmail($data['new_email'], (int) $this->authPlugin()->getUserId());

					$this->flashMessenger()->addSuccessMessage('Email address successfully updated!');
					return $this->redirect()->toRoute(
						'profile',
						 [
						 	'id' => $this->authPlugin()->getUserId(),
						 	'username' => mb_strtolower($this->authPlugin()->getUsername())
						 ]
					);
				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh();
				}
			}
		}

		return new ViewModel(['form' => $emailForm]);
	}

	public function indexAction()
	{	

		# only logged in users should see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}
		
		return new ViewModel();
	}

	public function passwordAction()
	{
		# only logged in users should see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		$passwordForm = new PasswordForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$passwordForm->setInputFilter($this->usersTable->getPasswordFormFilter()); 
			$passwordForm->setData($formData);

			if($passwordForm->isValid()) {
				$data = $passwordForm->getData();
				$hash = new Bcrypt();

				# compare password from passwordForm with one in database table
				if($hash->verify($data['current_password'], $this->authPlugin()->getPassword())) {
					# if all is well save the newly set password
					$this->usersTable->updatePassword($data['new_password'], (int)$this->authPlugin()->getUserId());

					$this->flashMessenger()->addSuccessMessage('Password successfully updated.');
					# guess what I am logging this person out
					# so that they signin with a new password
					# if u do not like that idea you can redirect to the profile page
					return $this->redirect()->toRoute('logout');

					/**
					  return $this->redirect()->toRoute('profile', ['id' => $this->authPlugin()->getUserId(), 'username' => mb_strtolower($this->authPlugin()->getUsername())]);
					*/
				}

			} else {
				$this->flashMessenger()->addErrorMessage('Incorrect current password!');
				return $this->redirect()->refresh();
			}
		}

		return new ViewModel(['form' => $passwordForm]);
	}


	public function usernameAction()
	{
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		$usernameForm = new UsernameForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$usernameForm->setInputFilter($this->usersTable->getUsernameFormFilter());
			$usernameForm->setData($formData);

			if($usernameForm->isValid()) {
				try {
					$data = $usernameForm->getData();
					$this->usersTable->updateUsername($data['new_username'], (int) $this->authPlugin()->getUserId());

					$this->flashMessenger()->addSuccessMessage('Username successfully updated');
					return $this->redirect()->toRoute('profile', ['id' => $this->authPlugin()->getUserId(), 'username' => mb_strtolower($this->authPlugin()->getUsername())]);
				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh();
				}
			}
		}

		return new ViewModel(['form' => $usernameForm]);
	}
}
