<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element;
use Laminas\Form\Form;

class PasswordForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('update_password');
		$this->setAttribute('metho', 'post');


		# current password field
		$this->add([
			'type' => Element\Password::class,
			'name' => 'current_password',
			'options' => [
				'label' => 'Current Password'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 25,
				'class' => 'form-control',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'title' => 'Provide your account\'s current password',
				'placeholder' => 'Enter Your Current Password'
			]
		]);

		#new_password field
		$this->add([
			'type' => Element\Password::class,
			'name' => 'new_password',
			'options' => [
				'label' => 'New Password'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 25,
				'class' => 'form-control',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'title' => 'Password must have at least 8 characters',
				'placeholder' => 'Enter Your Preferred New Password'
			]
		]);

		#confirm_new_password field
		$this->add([
			'type' => Element\Password::class,
			'name' => 'confirm_new_password',
			'options' => [
				'label' => 'Verify New Password'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 25,
				'class' => 'form-control',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'title' => 'Password must match that provided above',
				'placeholder' => 'Enter Your New Password Again'
			]
		]);

		$this->add([
			'type' => Element\Csrf::class,
			'name' => 'csrf',
			'options' => [
				'csrf_options' => [
					'timeout' => 600
				],
			],
		]);

		$this->add([
			'type' => Element\Submit::class,
			'name' => 'change_password',
			'attributes' => [
				'class' => 'btn btn-primary',
				'value' => 'Save Changes'
			]
		]);
	}
}
