<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:42
 */

namespace Domain\DTO;


/**
 * Class PenaltyEventData
 * @package Domain\DTO
 */
class PenaltyEventData
{
	private $penaltyTimeType;
	private $secondsFromStart;
	private $memberId;
	private $period;

	/**
	 * PenaltyEventData constructor.
	 * @param int $secondsFromStart
	 * @param int $period
	 * @param int $memberId
	 * @param string $penaltyTimeType
	 */
	public function __construct(int $secondsFromStart, int $period, int $memberId, string $penaltyTimeType)
	{
		$this->secondsFromStart = $secondsFromStart;
		$this->memberId = $memberId;
		$this->penaltyTimeType = $penaltyTimeType;
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
	 * @return string
	 */
	public function getPenaltyTimeType(): string
	{
		return $this->penaltyTimeType;
	}

	/**
	 * @return int
	 */
	public function getPeriod(): int
	{
		return $this->period;
	}
}