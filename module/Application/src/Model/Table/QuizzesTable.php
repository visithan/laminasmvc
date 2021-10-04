<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\QuizEntity;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Filter;
use Laminas\I18n;
use Laminas\InputFilter;
use Laminas\Validator;

class QuizzesTable extends AbstractTableGateway
{
	protected $adapter;
	protected $table = 'quizzes';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->initialize();
	}

	public function closeQuiz(int $quizId)
	{
		$sqlQuery = $this->sql->update()->set(['status' => '0'])->where(['quiz_id' => $quizId]);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}

	public function deleteQuizById(int $quizId)
	{
		$sqlQuery = $this->sql->update()->set(['status' => '0'])->where(['quiz_id' => $quizId]);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}

	# the correction should have been done here
	public function fetchAllMyQuizzes(int $userId)
	{
		$sqlQuery = $this->sql->select()
		    ->join('categories', 'categories.category_id='.$this->table.'.category_id', ['category'])
		    ->join('users', 'users.user_id='.$this->table.'.user_id', ['username'])
		    ->where([$this->table.'.status' => 1])
		    ->where([$this->table.'.user_id' => $userId])
		    ->order('created ASC');

		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute();

		$classMethod = new ClassMethodsHydrator();
		$entity      = new QuizEntity();

		$resultSet = new HydratingResultSet($classMethod, $entity);
		$resultSet->initialize($handler);

		return $resultSet;
	}

	public function fetchLatestQuizzes()
	{
		$sqlQuery = $this->sql->select()
		   ->join('categories', 'categories.category_id='.$this->table.'.category_id', ['category'])
		   ->order('created DESC');

		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute();

		$classMethod = new ClassMethodsHydrator();
		$entity      = new QuizEntity();
		$resultSet   = new HydratingResultSet($classMethod, $entity);
		$resultSet->initialize($handler);

		return $resultSet;
	}

	public function fetchQuizById(int $quizId)
	{
		$sqlQuery = $this->sql->select()
			->join('categories', 'categories.category_id='.$this->table.'.category_id', ['category_id','category'])
			->join('users', 'users.user_id='.$this->table.'.user_id', ['user_id', 'username'])
			->where([$this->table.'.quiz_id' => $quizId]);

		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();

		if(!$handler) {
			return null;
		}

		$classMethod = new ClassMethodsHydrator();
		$entity      = new QuizEntity();

		$classMethod->hydrate($handler, $entity);

		return $entity;
	}

	public function getCreateFormFilter()
	{
		$inputFilter = new InputFilter\InputFilter();
		$factory     = new InputFilter\Factory();

		# filter and validate quiz title
		$inputFilter->add(
			$factory->createInput([
				'name' => 'title',
				'required' => true,
				'filters' => [
					['name' => Filter\StripTags::class],
					['name' => Filter\StringTrim::class],
				],
				'validators' => [
					['name' => Validator\NotEmpty::class],
					[
						'name' => Validator\StringLength::class,
						'options' => [
							'min' => 4,
							'max' => 100,
							'messages' => [
								Validator\StringLength::TOO_SHORT => 'Quiz Title must have at least 4 characters',
								Validator\StringLength::TOO_LONG  => 'Quiz Title must have at most 100 characters',
							],
						],
					],
				],
			])
		);

		# filter and validate category_id field
		$inputFilter->add(
			$factory->createInput([
				'name' => 'category_id',
				'required' => true,
				'filters' => [
					['name' => Filter\StripTags::class],
					['name' => Filter\StringTrim::class],
					['name' => Filter\ToInt::class],
				],
				'validators' => [
					['name' => Validator\NotEmpty::class],
					['name' => I18n\Validator\IsInt::class],
					[
						'name' => Validator\Db\RecordExists::class,
						'options' => [
							'table' => 'categories',
							'field' => 'category_id',
							'adapter' => $this->adapter,
						],  
					],
				],
			])
		);

		# filter and validate timeout select field
		$inputFilter->add(
			$factory->createInput([
				'name' => 'timeout',
				'required' => true,
				'filters' => [
					['name' => Filter\StripTags::class],
					['name' => Filter\StringTrim::class],
				],
				'validators' => [
					['name' => Validator\NotEmpty::class],
					[
						'name' => Validator\InArray::class,
						'options' => [
							'haystack' => ['1 day', '3 days', '7 days'],
						],
					],
				],
			])
		);

		# filter and validate question textarea field
		$inputFilter->add(
			$factory->createInput([
				'name' => 'question',
				'required' => true,
				'filters' => [
					['name' => Filter\StripTags::class],
					['name' => Filter\StringTrim::class],
				],
				'validators' => [
					['name' => Validator\NotEmpty::class],
					[
						'name' => Validator\StringLength::class,
						'options' => [
							'min' => 5,
							'max' => 300,
							'messages' => [
								Validator\StringLength::TOO_SHORT => 'Question must have at least 5 characters',
								Validator\StringLength::TOO_LONG  => 'Question must have at most 300 characters',
							],
						],
					],
				],
			])
		);

		# filter and validate user_id field
		$inputFilter->add(
			$factory->createInput([
				'name' => 'user_id',
				'required' => true,
				'filters' => [
					['name' => Filter\StripTags::class],
					['name' => Filter\StringTrim::class],
					['name' => Filter\ToInt::class],
				],
				'validators' => [
					['name' => Validator\NotEmpty::class],
					['name' => I18n\Validator\IsInt::class],
					[
						'name' => Validator\Db\RecordExists::class,
						'options' => [
							'table' => 'users',
							'field' => 'user_id',
							'adapter' => $this->adapter,
						],
					],
				],
			])
		);

		# filter and validate the csrf field
		$inputFilter->add(
			$factory->createInput([
				'name' => 'csrf',
				'required' => true,
				'filters' => [
					['name' => Filter\StripTags::class],
					['name' => Filter\StringTrim::class],
				],
				'validators' => [
					['name' => Validator\NotEmpty::class],
					[
						'name' => Validator\Csrf::class,
						'options' => [
							'messages' => [
								Validator\Csrf::NOT_SAME => 'Fill the form again and try one more time',
							],
						],
					],
				],
			])
		);

		return $inputFilter;
	}

	public function saveQuiz(array $data)
	{
		$values = [
			'user_id'     => $data['user_id'],
			'category_id' => $data['category_id'],
			'title'       => $data['title'],
			'question'    => $data['question'],
			'timeout'     => date('Y-m-d H:i:s', strtotime("+" . $data['timeout'])),
			'created'     => date('Y-m-d H:i:s')
		];

		$sqlQuery = $this->sql->insert()->values($values);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}

	public function updateTotal(int $quizId)
	{
		$sqlQuery = $this->sql->update()
		       ->set(['total' => new Expression('total + 1')])
		       ->where(['quiz_id' => $quizId]);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}

	public function updateViews(int $quizId)
	{
		$sqlQuery = $this->sql->update()
		    ->set(['views' => new Expression('views + 1')])
		    ->where(['quiz_id' => $quizId]);

		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}
}
