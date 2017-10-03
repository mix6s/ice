<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 0:43
 */

namespace Domain\Entity;

use Domain\Exception\DomainException;


/**
 * Class GoalkeeperEvent
 * @package Domain\Entity
 */
class GoalkeeperEvent extends GameEvent
{
	private $id;
	private $game;
	private $bullets;
	private $goals;
	private $duration;
	private $member;

	/**
	 * GoalkeeperEvent constructor.
	 * @param int $id
	 * @param Game $game
	 * @param int $bullets
	 * @param int $goals
	 * @param int $duration duration in seconds
	 * @param SeasonTeamMember $member
	 * @throws DomainException
	 */
	public function __construct(int $id,  Game $game, int $bullets, int $goals, int $duration, SeasonTeamMember $member)
	{
		$this->id = $id;
		$this->game = $game;
		$this->bullets = $bullets;
		$this->goals = $goals;
		$this->duration = $duration;
		$this->member = $member;
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
			'id' => $this->getId(),
			'goals' => $this->getGoals(),
			'duration' => $this->getDuration(),
			'bullets' => $this->getBullets(),
			'game' => $this->getGame(),
			'member' => $this->getMember(),
		];
	}

	/**
	 * @return string
	 */
	function getType(): string
	{
		return 'goalkeeper';
	}

	/**
	 * @return int
	 */
	public function getPeriod(): int
	{
		return 0;
	}

	/**
	 * @return int
	 */
	public function getSecondsFromStart(): int
	{
		return 0;
	}

	/**
	 * @return int
	 */
	public function getBullets(): int
	{
		return $this->bullets;
	}

	/**
	 * @return int
	 */
	public function getGoals(): int
	{
		return $this->goals;
	}

	/**
	 * @return int
	 */
	public function getDuration(): int
	{
		return $this->duration;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getMember(): SeasonTeamMember
	{
		return $this->member;
	}
}