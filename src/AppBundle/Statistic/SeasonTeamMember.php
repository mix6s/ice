<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 10.10.2017
 * Time: 22:08
 */

namespace AppBundle\Statistic;


/**
 * Class SeasonTeamMember
 * @package AppBundle\Statistic
 */
class SeasonTeamMember
{
	private $gamesCount = 0;
	private $goals = 0;
	private $assistantGoals = 0;
	private $penaltyTime = 0;

	private $goalsFailed = 0;
	private $zeroGameCount = 0;
	private $totalSecondsTime = 0;

	/**
	 * @return int
	 */
	public function getGamesCount(): int
	{
		return $this->gamesCount;
	}

	/**
	 * @param int $gamesCount
	 */
	public function setGamesCount(int $gamesCount)
	{
		$this->gamesCount = $gamesCount;
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
	 * @return int
	 */
	public function getGoalsFailed(): int
	{
		return $this->goalsFailed;
	}

	/**
	 * @param int $goalsFailed
	 */
	public function setGoalsFailed(int $goalsFailed)
	{
		$this->goalsFailed = $goalsFailed;
	}

	/**
	 * @return int
	 */
	public function getZeroGameCount(): int
	{
		return $this->zeroGameCount;
	}

	/**
	 * @param int $zeroGameCount
	 */
	public function setZeroGameCount(int $zeroGameCount)
	{
		$this->zeroGameCount = $zeroGameCount;
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
}