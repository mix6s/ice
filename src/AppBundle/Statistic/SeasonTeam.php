<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 11.10.2017
 * Time: 21:30
 */

namespace AppBundle\Statistic;


/**
 * Class SeasonTeam
 * @package AppBundle\Statistic
 */
class SeasonTeam
{
	private $gamesCount = 0;
	private $winInMain = 0;
	private $winInBullets = 0;
	private $winInOvertime = 0;
	private $loseInMain = 0;
	private $loseInBullets = 0;
	private $loseInOvertime = 0;
	private $goals = 0;
	private $goalsFailed = 0;

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
	public function getWinInMain(): int
	{
		return $this->winInMain;
	}

	/**
	 * @param int $winInMain
	 */
	public function setWinInMain(int $winInMain)
	{
		$this->winInMain = $winInMain;
	}

	/**
	 * @return int
	 */
	public function getWinInBullets(): int
	{
		return $this->winInBullets;
	}

	/**
	 * @param int $winInBullets
	 */
	public function setWinInBullets(int $winInBullets)
	{
		$this->winInBullets = $winInBullets;
	}

	/**
	 * @return int
	 */
	public function getWinInOvertime(): int
	{
		return $this->winInOvertime;
	}

	/**
	 * @param int $winInOvertime
	 */
	public function setWinInOvertime(int $winInOvertime)
	{
		$this->winInOvertime = $winInOvertime;
	}

	/**
	 * @return int
	 */
	public function getLoseInMain(): int
	{
		return $this->loseInMain;
	}

	/**
	 * @param int $loseInMain
	 */
	public function setLoseInMain(int $loseInMain)
	{
		$this->loseInMain = $loseInMain;
	}

	/**
	 * @return int
	 */
	public function getLoseInBullets(): int
	{
		return $this->loseInBullets;
	}

	/**
	 * @param int $loseInBullets
	 */
	public function setLoseInBullets(int $loseInBullets)
	{
		$this->loseInBullets = $loseInBullets;
	}

	/**
	 * @return int
	 */
	public function getLoseInOvertime(): int
	{
		return $this->loseInOvertime;
	}

	/**
	 * @param int $loseInOvertime
	 */
	public function setLoseInOvertime(int $loseInOvertime)
	{
		$this->loseInOvertime = $loseInOvertime;
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
	public function getScores(): int
	{
		return $this->winInMain * 3 + ($this->winInBullets + $this->winInOvertime) * 2 + ($this->loseInBullets + $this->loseInOvertime) * 2;
	}
}