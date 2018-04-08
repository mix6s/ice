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
	private $goals = [];
	private $bullets = [];
	private $assistantGoals = [];
	private $penaltyTime = [];

	private $goalsFailedByType = [];
	private $totalSecondsTime = [];
	/**
	 * @var \Domain\Entity\SeasonTeamMember
	 */
	private $member;
	private $zeroGamesCount = [];
	private $gamesCountAsGoalkeeper = [];

	/**
	 * SeasonTeamMember constructor.
	 * @param \Domain\Entity\SeasonTeamMember $member
	 */
	public function __construct(\Domain\Entity\SeasonTeamMember $member)
	{
		$this->member = $member;
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getGamesCount(string $type = null): int
	{
		if ($type === null) {
			$count = 0;
			array_walk($this->games, function (array $games) use ($count) {
				$count += count($games);
			});
			return $count;
		}
		return count($this->games[$type] ?? []);
	}

	/**
	 * @param \Domain\Entity\Game $game
	 */
	private function setPlayedGame(\Domain\Entity\Game $game)
	{
		if (!array_key_exists((string)$game->getType(), $this->games)) {
			$this->games[(string)$game->getType()] = [];
		}
		if (in_array($game->getId(), $this->games[(string)$game->getType()])) {
			return;
		}
		$this->games[(string)$game->getType()][] = $game->getId();
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getGoals(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->goals);
		}
		return $this->goals[(string)$type] ?? 0;
	}

	/**
	 * @param int $goals
	 * @param string|null $type
	 */
	private function setGoals(int $goals, string $type)
	{
		$this->goals[(string)$type] = $goals;
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getAssistantGoals(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->assistantGoals);
		}
		return $this->assistantGoals[(string)$type] ?? 0;
	}

	/**
	 * @param int $assistantGoals
	 * @param string|null $type
	 */
	private function setAssistantGoals(int $assistantGoals, string $type)
	{
		$this->assistantGoals[(string)$type] = $assistantGoals;
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getPenaltyTime(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->penaltyTime);
		}
		return $this->penaltyTime[(string)$type] ?? 0;
	}

	/**
	 * @param int $penaltyTime
	 * @param string $type
	 */
	private function setPenaltyTime(int $penaltyTime, string $type)
	{
		$this->penaltyTime[(string)$type] = $penaltyTime;
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getScore(string $type = null): int
	{
		return $this->getAssistantGoals($type) + $this->getGoals($type);
	}


	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getGoalsFailedByType(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->goalsFailedByType);
		}
		return $this->goalsFailedByType[(string)$type] ?? 0;
	}


	/**
	 * @param int $goalsFailed
	 * @param string $type
	 */
	private function setGoalsFailedByType(int $goalsFailed, string $type)
	{
		$this->goalsFailedByType[(string)$type] = $goalsFailed;
		if ($goalsFailed === 0) {
			$this->zeroGamesCount[(string)$type] = ($this->zeroGamesCount[(string)$type] ?? 0) + 1;
		}
	}

	/**
	 * @return int
	 */
	public function getZeroGameCount(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->zeroGamesCount);
		}
		return $this->zeroGamesCount[(string)$type] ?? 0;
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getTotalSecondsTime(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->totalSecondsTime);
		}
		return $this->totalSecondsTime[(string)$type] ?? 0;
	}

	/**
	 * @param int $totalSecondsTime
	 * @param string $type
	 */
	private function setTotalSecondsTime(int $totalSecondsTime, string $type)
	{
		$this->totalSecondsTime[(string)$type] = $totalSecondsTime;
	}

	/**
	 * @return float
	 */
	public function getReliabilityCoef(string $type = null): float
	{
		if ($this->getTotalMinutesTime($type) === 0.) {
			return 0;
		}
		return 60 * $this->getGoalsFailedByType($type) / $this->getTotalMinutesTime($type);
	}

	/**
	 * @return float
	 */
	public function getReflectedBulletsPercent(string $type = null): float
	{
		if ($this->getBullets($type) === 0) {
			return 0;
		}
		return ($this->getBullets($type) - $this->getGoalsFailedByType($type)) / $this->getBullets($type) * 100;
	}

	/**
	 * @return float
	 */
	public function getTotalMinutesTime(string $type = null): float
	{
		return $this->getTotalSecondsTime($type) / 60;
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getGamesCountAsGoalkeeper(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->gamesCountAsGoalkeeper);
		}
		return $this->gamesCountAsGoalkeeper[(string)$type] ?? 0;
	}

	/**
	 * @param int $value
	 * @param string $type
	 */
	public function setGamesCountAsGoalkeeper(int $value, string $type)
	{
		$this->gamesCountAsGoalkeeper[(string)$type] = $value;
	}

	/**
	 * @return int
	 */
	public function getForwardScore(string $type = null)
	{
		return $this->getGoals($type) + $this->getAssistantGoals($type);
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
		if (!in_array($this->member->getId(), $game->getMembersA()) && !in_array($this->member->getId(), $game->getMembersB())) {
			return;
		}
		$this->setPlayedGame($game);
		switch ($event->getType()) {
			case 'goalkeeper':
				/** @var GoalkeeperEvent $event */
				if ($event->getMember()->getId() === $this->getMember()->getId()) {
					$memberIsGoalkeeper = true;
					$memberGameGoalsFailed = $event->getGoals();
					$this->setBullets($this->getBullets($game->getType()) + $event->getBullets(), $game->getType());
					$this->setGoalsFailedByType($this->getGoalsFailedByType($game->getType()) + $event->getGoals(), $game->getType());
					$this->setTotalSecondsTime($this->getTotalSecondsTime($game->getType()) + $event->getDuration(), $game->getType());
					$this->setGamesCountAsGoalkeeper($this->getGamesCountAsGoalkeeper($game->getType()) + 1, $game->getType());
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
					$this->setGoals($this->getGoals($game->getType()) + 1, $game->getType());
				} elseif (in_array($this->getMember()->getId(), $assistants)) {
					$this->setAssistantGoals($this->getAssistantGoals($game->getType()) + 1, $game->getType());
				}
				break;
			case 'penalty':
				/** @var PenaltyEvent $event */
				if ($event->getMember()->getId() === $this->getMember()->getId()) {
					$this->setPenaltyTime($this->getPenaltyTime($game->getType()) + $event->getPenaltyTime(), $game->getType());
				}
				break;
			default:
				break;
		}
	}

	/**
	 * @param string|null $type
	 * @return int
	 */
	public function getBullets(string $type = null): int
	{
		if ($type === null) {
			return array_sum($this->bullets);
		}
		return $this->bullets[(string)$type] ?? 0;
	}

	/**
	 * @param int $bullets
	 * @param string $type
	 */
	private function setBullets(int $bullets, string $type)
	{
		$this->bullets[(string)$type] = $bullets;
	}
}