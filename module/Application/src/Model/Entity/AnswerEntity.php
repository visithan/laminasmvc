<?php

declare(strict_types=1);

namespace Application\Model\Entity;

class AnswerEntity
{
	protected $answer_id;
	protected $quiz_id;
	protected $answer;
	protected $tally;
	#tallies table columns
	protected $user_id;
	protected $created;


	public function getAnswerId()
	{
		return $this->answer_id;
	}

	public function setAnswerId($answerId)
	{
		$this->answer_id = $answerId;
	}

	public function getQuizId()
	{
		return $this->quiz_id;
	}

	public function setQuizId($quizId)
	{
		$this->quiz_id = $quizId;
	}

	public function getAnswer()
	{
		return $this->answer;
	}

	public function setAnswer($answer)
	{
		$this->answer = $answer;
	}

	public function getTally()
	{
		return $this->tally;
	}

	public function setTally($tally)
	{
		$this->tally = $tally;
	}

	public function getUserId()
	{
		return $this->user_id;
	}

	public function setUserId($userId)
	{
		$this->user_id = $userId;
	}

	public function getCreated()
	{
		return $this->created;
	}

	public function setCreated($created)
	{
		$this->created = $created;
	}
}

