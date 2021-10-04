<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element;
use Laminas\Form\Form;

class DeleteForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('deactivate_account');
		$this->setAttribute('method', 'post');

		$this->add([
			'type' => Element\Hidden::class,
			'name' => 'user_id'
		]);

		$this->add([
			'type' => Element\Submit::class,
			'name' => 'delete_account',
			'attributes' => [
				'class' => 'btn btn-primary'
			]
		]);
	}
}
