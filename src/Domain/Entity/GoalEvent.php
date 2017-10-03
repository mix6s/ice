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
 * Class GoalEvent
 * @package Domain\Entity
 */
class GoalEvent extends GameEvent
{
	private $id;
	private $game;
	private $secondsFromStart;
	private $member;
	private $assistantA;
	private $assistantB;
	private $period;

	/**
	 * GoalEvent constructor.
	 * @param int $id
	 * @param int $period
	 * @param Game $game
	 * @param int $secondsFromStart
	 * @param SeasonTeamMember $member
	 * @param SeasonTeamMember|null $assistantA
	 * @param SeasonTeamMember|null $assistantB
	 * @throws DomainException
	 */
	public function __construct(int $id, int $period, Game $game, int $secondsFromStart, SeasonTeamMember $member, SeasonTeamMember $assistantA = null, SeasonTeamMember $assistantB = null)
	{
		if (!in_array($member->getSeasonTeam()->getId() ,[$game->getSeasonTeamB()->getId(), $game->getSeasonTeamA()->getId()])) {
			throw new DomainException("Member does not exist in game season teams members");
		}
		if (($assistantA && $member->getSeasonTeam()->getId() !== $assistantA->getSeasonTeam()->getId())
			|| ($assistantB && $member->getSeasonTeam()->getId() !== $assistantB->getSeasonTeam()->getId())) {
			throw new DomainException("Season team members in different season teams");
		}
		$this->id = $id;
		$this->period = $period;
		$this->game = $game;
		$this->secondsFromStart = $secondsFromStart;
		$this->member = $member;
		$this->assistantA = $assistantA;
		$this->assistantB = $assistantB;
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
	 * @return SeasonTeamMember|null
	 */
	public function getAssistantA()
	{
		return $this->assistantA;
	}

	/**
	 * @return SeasonTeamMember|null
	 */
	public function getAssistantB()
	{
		return $this->assistantB;
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
			'assistant_a' => $this->getAssistantA(),
			'assistant_b' => $this->getAssistantB(),
		];
	}

	/**
	 * @return string
	 */
	function getType(): string
	{
		return 'goal';
	}

	/**
	 * @return int
	 */
	public function getPeriod(): int
	{
		return $this->period;
	}
}