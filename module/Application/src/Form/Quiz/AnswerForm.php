<?php

declare(strict_types=1);

namespace Application\Form\Quiz;

use Laminas\Form\Element;
use Laminas\Form\Form;

class AnswerForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('choose_answer');
		$this->setAttribute('method', 'post');

		# answer radio button
		$this->add([
			'type' => Element\Radio::class,
			'name' => 'answer_id',
			'options' => [
				'label_attributes' => [
					'class' => 'form-check-label'
				],
				'disable_inarray_validator' => true,
			],
			'attributes' => [
				'required' => true,
				'class' => 'form-check-input'
			]
		]);

		# user_id hidden field
		$this->add([
			'type' => Element\Hidden::class,
			'name' => 'user_id'
		]);

		$this->add([
			'type' => Element\Csrf::class,
			'name' => 'csrf',
			'options' => [
				'csrf_options' => [
					'timeout' => 3600,
				]
			]
		]);

		$this->add([
			'type' => Element\Submit::class,
			'name' => 'select_answer',
			'attributes' => [
				'value' => 'Save Answer',
				'class' => 'btn btn-sm btn-primary'
			]
		]);
	}
}
