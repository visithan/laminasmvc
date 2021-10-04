<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\Auth\CreateForm;
use User\Model\Table\UsersTable;

class AuthController extends AbstractActionController
{
	private $usersTable;

	public function __construct(UsersTable $usersTable)
	{
		$this->usersTable = $usersTable;
	}

	public function createAction()
	{
		# make sure only visitors with no session access this page
		$auth = new AuthenticationService();
		if($auth->hasIdentity()) {
			# if user has session take them some else
			return $this->redirect()->toRoute('home');
		}

	    $createForm = new CreateForm();
	    $request = $this->getRequest();

	    if($request->isPost()) {
	    	$formData = $request->getPost()->toArray();
	    	$createForm->setInputFilter($this->usersTable->getCreateFormFilter());
	    	$createForm->setData($formData);

	    	if($createForm->isValid()) {
	    		try {
	    			$data = $createForm->getData();
	    			$this->usersTable->saveAccount($data); 

	    			$this->flashMessenger()->addSuccessMessage('Account successfully created. You can now login');

	    			return $this->redirect()->toRoute('login');
	    		} catch(RuntimeException $exception) {
	    			$this->flashMessenger()->addErrorMessage($exception->getMessage());
	    			return $this->redirect()->refresh(); # refresh this page to view errors
	    		}
	    	}
	    }

		return new ViewModel(['form' => $createForm]);
	}
}
