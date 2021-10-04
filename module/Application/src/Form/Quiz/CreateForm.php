<?php

declare(strict_types=1);

namespace Application\Form\Quiz;

use Application\Model\Table\CategoriesTable;
use Laminas\Form\Element;
use Laminas\Form\Form;

class CreateForm extends Form
{
	public function __construct(CategoriesTable $categoriesTable)
	{
		parent::__construct('new_quiz');
		$this->setAttribute('method', 'post');

		# title input field
		$this->add([
			'type' => Element\Text::class,
			'name' => 'title',
			'options' => [
				'label' => 'Quiz Title'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 100,
				'class' => 'form-control',
				'placeholder' => 'Enter quiz title',
				'data-toggle' => 'tooltip',
				'title' => 'Provide quiz title'
			]
		]);

		# categories select field
		$this->add([
			'type' => Element\Select::class,
			'name' => 'category_id',
			'options' => [
				'label' => 'Quiz Categories',
				'empty_option' => 'Select..',
				'value_options' => $categoriesTable->fetchAllCategories()
			],
			'attributes' => [
				'required' => true,
				'class' => 'custom-select'
			]
		]);

		#timeout select field
		$this->add([
			'type' => Element\Select::class,
			'name' => 'timeout',
			'options' => [
				'label' => 'Quiz Ends In',
				'empty_option' => 'Select...',
				'value_options' => [
					'1 day'  => '1 Day',
					'3 days' => '3 Days',
					'7 days' => '7 Days',
				]
			],
			'attributes' => [
				'required' => true,
				'class' => 'custom-select d-block w-100'
			]
		]);

		# question text area
		$this->add([
			'type' => Element\Textarea::class,
			'name' => 'question',
			'options' => [
				'label' => 'Quiz Question'
			],
			'attributes' => [
				'required' => true,
				'cols' => 90,
				'maxlength' => 300,
				'class' => 'form-control',
				'placeholder' => 'Write your question...',
				'data-toggle' => 'tooltip',
				'title' => 'Provide a question'
			]
		]);

		$this->add([
			'type' => Element\Text::class,
			'name' => 'answers[]',
			'options' => [
				'label' => 'Quiz Answers'
			],
			'attributes' => [
				'required' => true,
				'size' => 40,
				'maxlength' => 100,
				'class' => 'form-control',
				'placeholder' => 'Enter a possible answer',
				'data-toggle' => 'tooltip',
				'title' => 'Provide a possible answer'
			]
		]);

		$this->add([
			'type' => Element\Hidden::class,
			'name' => 'user_id'
		]);

		$this->add([
			'type' => Element\Button::class,
			'name' => 'add_more',
			'options' => [
				'label' => 'Add Another Answer'
			],
			'attributes' => [
				'class' => 'btn btn-sm btn-secondary',
				'id' => 'add_more'
			]
		]);

		$this->add([
			'type' => Element\Csrf::class,
			'name' => 'csrf',
			'options' => [
				'csrf_options' => [
					'timeout' => 600,
				]
			]
		]);

		$this->add([
			'type' => Element\Submit::class,
			'name' => 'create_quiz',
			'attributes' => [
				'value' => 'Post Quiz',
				'class' => 'btn btn-primary'
			]
		]);
	}
}
