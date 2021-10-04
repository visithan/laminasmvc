<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\ELement;
use Laminas\Form\Form;

class UsernameForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('update_username');
		$this->setAttribute('method', 'post');

		$this->add([
			'type' => Element\Text::class,
			'name' => 'current_username',
			'options' => [
				'label' => 'Current Username',
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 25,
				'pattern' => '^[a-zA-Z0-9]+$',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',
				'title' => 'Current Username',
				'readonly' => true,	
			]
		]);

		$this->add([
			'type' => Element\Text::class,
			'name' => 'new_username',
			'options' => [
				'label' => 'New Username',
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 25,
				'pattern' => '^[a-zA-Z0-9]+$',
				'autocomplete' => false,
				'data-toggle' => 'tooltip',
				'class' => 'form-control',
				'title' => 'Username must consist of alphanumeric characters only',
				'placeholder' => 'Enter Your Preferred Username'
			]
		]);


		$this->add([
			'type' => Element\Csrf::class,
			'name' => 'csrf',
			'options' => [
				'csrf_options' => [
					'timeout' => 300
				],
			],
		]);


		$this->add([
			'type' => Element\Submit::class,
			'name' => 'change_username',
			'attributes' => [
				'class' => 'btn btn-primary',
				'value' => 'Save Changes'
			]
		]);
	}
}
