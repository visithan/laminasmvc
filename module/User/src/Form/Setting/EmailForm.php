<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element;
use Laminas\Form\Form;

class EmailForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('update_email');
		$this->setAttribute('method', 'post');

		$this->add([
			'type' => Element\Email::class,
			'name' => 'current_email',
			'options' => [
				'label' => 'Current Email Address'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 128,
				'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',
				'title' => 'Current Email Address',
				'readonly' => true,
			]
		]);

		$this->add([
			'type' => Element\Email::class,
			'name' => 'new_email',
			'options' => [
				'label' => 'New Email Address'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 128,
				'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',
				'placeholder' => 'Enter a new email address',
				'title' => 'Provide a valid and working email address'
			]
		]);

		$this->add([
			'type' => Element\Email::class,
			'name' => 'confirm_new_email',
			'options' => [
				'label' => 'Verify New Email Address'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 128,
				'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',
				'placeholder' => 'Enter your new email address again',
				'title' => 'Email address must match that provided above'
			]
		]);

		$this->add([
			'type' => Element\Csrf::class,
			'name' => 'csrf',
			'options' => [
				'csrf_options' => [
					'timeout' => 600
				]
			]
		]);

		$this->add([
			'type' => Element\Submit::class,
			'name' => 'change_email',
			'attributes' => [
				'class' => 'btn btn-primary',
				'value' => 'Save Changes'
			]
		]);
	}
}
