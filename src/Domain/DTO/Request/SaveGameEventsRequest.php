<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:21
 */

namespace Domain\DTO\Request;


use Domain\DTO\GoalEventData;
use Domain\DTO\GoalkeeperData;
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
	 * @param GoalEventData $data
	 */
	public function addGoalEventData(GoalEventData $data)
	{
		$this->gameEventsData[] = $data;
	}

	/**
	 * @param PenaltyEventData $data
	 */
	public function addPenaltyEventData(PenaltyEventData $data)
	{
		$this->gameEventsData[] = $data;
	}

	/**
	 * @param GoalkeeperData $data
	 */
	public function addGoalkeeperData(GoalkeeperData $data)
	{
		$this->gameEventsData[] = $data;
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