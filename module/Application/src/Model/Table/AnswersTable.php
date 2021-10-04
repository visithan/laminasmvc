<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\AnswerEntity;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Filter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\I18n;
use Laminas\InputFilter;
use Laminas\Validator;

class AnswersTable extends AbstractTableGateway
{
	protected $adapter;
	protected $table = 'answers';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->initialize();
	}

	public function fetchAnswersById(int $quizId)
	{
		$sqlQuery = $this->sql->select()->where([$this->table.'.quiz_id' => $quizId]);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler  = $sqlStmt->execute();

		$classMethod = new ClassMethodsHydrator();
		$entity      = new AnswerEntity();

		$resultSet = new HydratingResultSet($classMethod, $entity);
		$resultSet->initialize($handler);

		return $resultSet;
	}

	public function getAnswerFormFilter()
	{
		$inputFilter = new InputFilter\InputFilter();
		$factory     = new InputFilter\Factory();

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

		#filter and validate answer_id
		$inputFilter->add(
			$factory->createInput([
				'name' => 'answer_id',
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
							'table' => $this->table,
							'field' => 'answer_id',
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

	public function updateAnswerTally(int $answerId, int $quizId)
	{
		$sqlQuery = $this->sql->update()
		    ->set(['tally' => new Expression('tally + 1')])
		    ->where(['answer_id' => $answerId])
		    ->where(['quiz_id' => $quizId]);

		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}

	public function saveAnswer(string $answer, int $quizId)
	{
		$values = [
			'quiz_id' => $quizId,
			'answer'  => $answer
		];

		$sqlQuery = $this->sql->insert()->values($values);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

		return $sqlStmt->execute();
	}
}

# well that was a lot of coding. I will go through the video one more time to check for
# errors. Then we test the feature together. See u in the next video.