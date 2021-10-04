<?php

declare(strict_types=1);

namespace Application\Form\Help;

use Laminas\Captcha\Recaptcha;
use Laminas\Form\Element;
use Laminas\Form\Form;

class ContactForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('get_in_touch');
		$this->setAttribute('method', 'post');

		$this->add([
			'type' => Element\Text::class,
			'name' => 'your_name',
			'options' => [
				'label' => 'Full Name',
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 50,
				'class' => 'form-control',
				'data-toggle' => 'tooltip',
				'title' => 'Provide you name in full (first and last name)',
				'placeholder' => 'Enter Your Full Name',
			]
		]);

		$this->add([
			'type' => Element\Email::class,
			'name' => 'your_email',
			'options' => [
				'label' => 'Email Address',
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 128,
				'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
				'class' => 'form-control',
				'data-toggle' => 'tooltip',
				'title' => 'Provide your email address',
				'placeholder' => 'Enter Your Email Address',
			]
		]);

		$this->add([
			'type' => Element\Textarea::class,
			'name' => 'message',
			'options' => [
				'label' => 'Message Content',
			],
			'attributes' => [
				'required' => true,
				'cols' => 90,
				'maxlength' => 2500,
				'class' => 'form-control',
				'data-toggle' => 'tooltip',
				'title' => 'Provide your message',
				'placeholder' => 'Write your message..',
			]
		]);


		$this->add([
			'type' => Element\Captcha::class,
			'name' => 'turing',
			'options' => [
				'label' => 'Verify that you are human',
				'captcha' => new Recaptcha([
					#'secret_key' => '6LdL4EAUAAAAAHs48MK7DuPNBNKmvwruirzAYigF', # this was for example.com
					#
					#'site_key' => '6LdL4EAUAAAAABXDX9EPvyPR949BUmwi-ItrcOVQ',

					'secret_key' => '6LecZekUAAAAAAEFxtbrEe2WvxtsHGB7ZfBXO4mo',
					'site_key' => '6LecZekUAAAAAMYyjYpo7h4qDNEy6Lm3-yQFEr64',
				]),
			],
			'attributes' => [
				'required' => true,
				'class' => 'form-control'
			]
		]);

		$this->add([
			'type' => Element\Csrf::class,
			'name' => 'csrf',
			'options' => [
				'csrf_options' => [
					'timeout' => 600,
				],
			],
		]);

		$this->add([
			'type' => Element\Submit::class,
			'name' => 'contact_us',
			'attributes' => [
				'value' => 'Send Message',
				'class' => 'btn btn-primary'
			]
		]);
	}
}
