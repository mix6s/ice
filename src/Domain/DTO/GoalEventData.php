<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:28
 */

namespace Domain\DTO;


/**
 * Class GoalEventData
 * @package Domain\DTO
 */
class GoalEventData
{
	private $secondsFromStart;
	private $memberId;
	private $assistantAId;
	private $assistantBId;
	private $period;

	/**
	 * GoalEventData constructor.
	 * @param int $secondsFromStart
	 * @param int $period
	 * @param int $memberId
	 * @param int $assistantAId
	 * @param int $assistantBId
	 */
	public function __construct(int $secondsFromStart, int $period, int $memberId, int $assistantAId = null, int $assistantBId = null)
	{
		$this->secondsFromStart = $secondsFromStart;
		$this->memberId = $memberId;
		$this->assistantAId = $assistantAId;
		$this->assistantBId = $assistantBId;
		$this->period = $period;
	}

	/**
	 * @return int
	 */
	public function getSecondsFromStart(): int
	{
		return $this->secondsFromStart;
	}

	/**
	 * @return int
	 */
	public function getMemberId(): int
	{
		return $this->memberId;
	}

	/**
	 * @return int|null
	 */
	public function getAssistantAId()
	{
		return $this->assistantAId;
	}

	/**
	 * @return int|null
	 */
	public function getAssistantBId()
	{
		return $this->assistantBId;
	}

	/**
	 * @return int
	 */
	public function getPeriod(): int
	{
		return $this->period;
	}
}