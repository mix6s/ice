<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 10.10.2017
 * Time: 22:08
 */

namespace AppBundle\Statistic;

use Domain\Entity\GameEvent;
use Domain\Entity\GoalEvent;
use Domain\Entity\GoalkeeperEvent;
use Domain\Entity\PenaltyEvent;


/**
 * Class SeasonTeamMember
 * @package AppBundle\Statistic
 */
class SeasonTeamMember
{
	private $games = [];
	private $goals = 0;
	private $assistantGoals = 0;
	private $penaltyTime = 0;

	private $goalsFailed = [];
	private $totalSecondsTime = 0;
	/**
	 * @var \Domain\Entity\SeasonTeamMember
	 */
	private $member;

	/**
	 * SeasonTeamMember constructor.
	 * @param \Domain\Entity\SeasonTeamMember $member
	 */
	public function __construct(\Domain\Entity\SeasonTeamMember $member)
	{
		$this->member = $member;
	}

	/**
	 * @return int
	 */
	public function getGamesCount(): int
	{
		return count($this->games);
	}

	/**
	 * @param \Domain\Entity\Game $game
	 */
	public function setPlayedGame(\Domain\Entity\Game $game)
	{
		if (in_array($game->getId(), $this->games)) {
			return;
		}
		$this->games[] = $game->getId();
	}

	/**
	 * @return int
	 */
	public function getGoals(): int
	{
		return $this->goals;
	}

	/**
	 * @param int $goals
	 */
	public function setGoals(int $goals)
	{
		$this->goals = $goals;
	}

	/**
	 * @return int
	 */
	public function getAssistantGoals(): int
	{
		return $this->assistantGoals;
	}

	/**
	 * @param int $assistantGoals
	 */
	public function setAssistantGoals(int $assistantGoals)
	{
		$this->assistantGoals = $assistantGoals;
	}

	/**
	 * @return int
	 */
	public function getPenaltyTime(): int
	{
		return $this->penaltyTime;
	}

	/**
	 * @param int $penaltyTime
	 */
	public function setPenaltyTime(int $penaltyTime)
	{
		$this->penaltyTime = $penaltyTime;
	}

	/**
	 * @return int
	 */
	public function getScore(): int
	{
		return $this->getAssistantGoals() + $this->getGoals();
	}

	/**
	 * @param \Domain\Entity\Game|null $game
	 * @return int
	 */
	public function getGoalsFailed(\Domain\Entity\Game $game = null): int
	{
		if ($game === null) {
			return array_sum($this->goalsFailed);
		}
		return $this->goalsFailed[$game->getId()] ?? 0;
	}

	/**
	 * @param int $goalsFailed
	 * @param \Domain\Entity\Game $game
	 */
	public function setGoalsFailed(int $goalsFailed, \Domain\Entity\Game $game)
	{
		$this->goalsFailed[$game->getId()] = $goalsFailed;
	}

	/**
	 * @return int
	 */
	public function getZeroGameCount(): int
	{
		return count(
			array_filter(
				$this->goalsFailed,
				function ($goals) {
					return $goals == 0;
				}
			)
		);
	}

	/**
	 * @return int
	 */
	public function getTotalSecondsTime(): int
	{
		return $this->totalSecondsTime;
	}

	/**
	 * @param int $totalSecondsTime
	 */
	public function setTotalSecondsTime(int $totalSecondsTime)
	{
		$this->totalSecondsTime = $totalSecondsTime;
	}

	/**
	 * @return float
	 */
	public function getReliabilityCoef(): float
	{
		if ($this->getTotalMinutesTime() === 0.) {
			return 0;
		}
		return 60 * $this->getGoalsFailed() / $this->getTotalMinutesTime();
	}

	/**
	 * @return float
	 */
	public function getTotalMinutesTime(): float
	{
		return $this->getTotalSecondsTime() / 60;
	}

	/**
	 * @return int
	 */
	public function getGamesCountAsGoalkeeper(): int
	{
		return count($this->goalsFailed);
	}


	/**
	 * @return int
	 */
	public function getForwardScore()
	{
		return $this->getGoals() + $this->getAssistantGoals();
	}

	/**
	 * @return \Domain\Entity\SeasonTeamMember
	 */
	public function getMember(): \Domain\Entity\SeasonTeamMember
	{
		return $this->member;
	}

	/**
	 * @param \Domain\Entity\Game $game
	 * @param \Domain\Entity\GameEvent $event
	 */
	public function aggregate(\Domain\Entity\Game $game, GameEvent $event)
	{
		$this->setPlayedGame($game);
		switch ($event->getType()) {
			case 'goalkeeper':
				/** @var GoalkeeperEvent $event */
				if ($event->getMember()->getId() === $this->getMember()->getId()) {
					$memberIsGoalkeeper = true;
					$memberGameGoalsFailed = $event->getGoals();
					$this->setGoalsFailed($this->getGoalsFailed($game) + $event->getGoals(), $game);
					$this->setTotalSecondsTime($this->getTotalSecondsTime() + $event->getDuration());
				}
				break;
			case 'goal':
				/** @var GoalEvent $event */
				$assistants = [];
				if ($event->getAssistantA()) {
					$assistants[] = $event->getAssistantA()->getId();
				}
				if ($event->getAssistantB()) {
					$assistants[] = $event->getAssistantB()->getId();
				}
				if ($event->getMember()->getId() === $this->getMember()->getId()) {
					$this->setGoals($this->getGoals() + 1);
				} elseif (in_array($this->getMember()->getId(), $assistants)) {
					$this->setAssistantGoals($this->getAssistantGoals() + 1);
				}
				break;
			case 'penalty':
				/** @var PenaltyEvent $event */
				if ($event->getMember()->getId() === $this->getMember()->getId()) {
					$this->setPenaltyTime($this->getPenaltyTime() + $event->getPenaltyTime());
				}
				break;
			default:
				break;
		}
	}
}