<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:21
 */

namespace Domain\DTO\Request;


use Domain\DTO\GoalEventData;
use Domain\DTO\PenaltyEventData;

/**
 * Class SaveGameEventsRequest
 * @package Domain\DTO\Request
 */
class SaveGameEventsRequest
{
	private $gameId;
	private $gameEventsData = [];

	/**
	 * SaveGameEventsRequest constructor.
	 * @param int $gameId
	 */
	public function __construct(int $gameId)
	{
		$this->gameId = $gameId;
		$this->gameEventsData = [];
	}

	/**
	 * @param string $timeFormatted
	 * @param int $memberId
	 * @param int|null $assistantAId
	 * @param int|null $assistantBId
	 */
	public function addGoalEventData(string $timeFormatted, int $memberId, int $assistantAId = null, int $assistantBId = null)
	{
		$this->gameEventsData[] = new GoalEventData($this->timeToSeconds($timeFormatted), $memberId, $assistantAId, $assistantBId);
	}

	/**
	 * /**
	 * @param string $timeFormatted
	 * @param int $memberId
	 * @param string $penaltyTimeType
	 */
	public function addPenaltyEventData(string $timeFormatted, int $memberId, string $penaltyTimeType)
	{
		$this->gameEventsData[] = new PenaltyEventData($this->timeToSeconds($timeFormatted), $memberId, $penaltyTimeType);
	}

	private function timeToSeconds(string $time)
	{
		$seconds = 0;
		$parts = explode(':', $time);
		if (!empty($parts[0])) {
			$seconds += !empty($parts[1]) ? $parts[0] * 60 : $parts[0];
		}

		if (!empty($parts[1])) {
			$seconds += $parts[0];
		}
		return $seconds;
	}

	/**
	 * @return array
	 */
	public function getGameEventsData(): array
	{
		return $this->gameEventsData;
	}

	/**
	 * @return int
	 */
	public function getGameId(): int
	{
		return $this->gameId;
	}
}