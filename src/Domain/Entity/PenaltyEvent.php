<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 1:03
 */

namespace Domain\Entity;

use Domain\Exception\DomainException;


/**
 * Class PenaltyEvent
 * @package Domain\Entity
 */
class PenaltyEvent extends GameEvent
{
	private $game;
	private $id;
	private $secondsFromStart;
	private $penaltyTimeType;
	private $member;
	private $period;

	const PENALTY_TIME_TYPE_2 = '2';
	const PENALTY_TIME_TYPE_2_2 = '2_2';
	const PENALTY_TIME_TYPE_5_20 = '5_20';
	const PENALTY_TIME_TYPE_10 = '10';

	/**
	 * PenaltyEvent constructor.
	 * @param int $id
	 * @param int $period
	 * @param Game $game
	 * @param int $secondsFromStart
	 * @param SeasonTeamMember $member
	 * @param string $penaltyTimeType
	 * @throws DomainException
	 */
	public function __construct(int $id, int $period, Game $game, int $secondsFromStart, SeasonTeamMember $member, string $penaltyTimeType)
	{
		if (!in_array(
			$penaltyTimeType,
			[
				self::PENALTY_TIME_TYPE_2,
				self::PENALTY_TIME_TYPE_2_2,
				self::PENALTY_TIME_TYPE_5_20,
				self::PENALTY_TIME_TYPE_10
			]
		)) {
			throw new DomainException("Invalid penalty time type");
		}
		if (!in_array(
			$member->getSeasonTeam()->getId(),
			[$game->getSeasonTeamB()->getId(), $game->getSeasonTeamA()->getId()]
		)) {
			throw new DomainException("Member does not exist in game season teams members");
		}
		$this->id = $id;
		$this->period = $period;
		$this->game = $game;
		$this->secondsFromStart = $secondsFromStart;
		$this->member = $member;
		$this->penaltyTimeType = $penaltyTimeType;
	}

	/**
	 * @return Game
	 */
	public function getGame(): Game
	{
		return $this->game;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getSecondsFromStart(): int
	{
		return $this->secondsFromStart;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getMember(): SeasonTeamMember
	{
		return $this->member;
	}
	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return [
			'type' => $this->getType(),
			'period' => $this->getPeriod(),
			'id' => $this->getId(),
			'seconds_from_start' => $this->getSecondsFromStart(),
			'game' => $this->getGame(),
			'member' => $this->getMember(),
			'penalty_time_type' => $this->getPenaltyTimeType()
		];
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
	public function getPenaltyTime(): int
	{
		switch ($this->getPenaltyTimeType()) {
			case PenaltyEvent::PENALTY_TIME_TYPE_2:
				return 2;
			case PenaltyEvent::PENALTY_TIME_TYPE_5_20:
				return 20;
			case PenaltyEvent::PENALTY_TIME_TYPE_2_2:
				return 4;
			default:
				return 0;
		}
	}

	/**
	 * @return string
	 */
	function getType(): string
	{
		return 'penalty';
	}

	/**
	 * @return int
	 */
	public function getPeriod(): int
	{
		return $this->period;
	}
}