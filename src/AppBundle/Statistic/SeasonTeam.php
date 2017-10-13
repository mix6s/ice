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
	private $seasonTeam;
	private $gamesCount = 0;
	private $winInMain = [];
	private $winInBullets = [];
	private $winInOvertime = [];
	private $loseInMain = [];
	private $loseInBullets = [];
	private $loseInOvertime = [];
	private $goals = 0;
	private $goalsFailed = 0;

	/**
	 * SeasonTeam constructor.
	 * @param \Domain\Entity\SeasonTeam $seasonTeam
	 */
	public function __construct(\Domain\Entity\SeasonTeam $seasonTeam)
	{
		$this->seasonTeam = $seasonTeam;
	}

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
	 * @param \Domain\Entity\SeasonTeam|null $team
	 * @return int
	 */
	public function getWinInMain(\Domain\Entity\SeasonTeam $team = null): int
	{
		if ($team === null) {
			return array_sum($this->winInMain);
		}
		return $this->winInMain[$team->getId()] ?? 0;
	}

	/**
	 * @param int $winInMain
	 * @param \Domain\Entity\SeasonTeam $team
	 */
	public function setWinInMain(int $winInMain, \Domain\Entity\SeasonTeam $team)
	{
		$this->winInMain[$team->getId()] = $winInMain;
	}

	/**
	 * @param \Domain\Entity\SeasonTeam|null $team
	 * @return int
	 */
	public function getWinInBullets(\Domain\Entity\SeasonTeam $team = null): int
	{
		if ($team === null) {
			return array_sum($this->winInBullets);
		}
		return $this->winInBullets[$team->getId()] ?? 0;
	}

	/**
	 * @param int $winInBullets
	 * @param \Domain\Entity\SeasonTeam $team
	 */
	public function setWinInBullets(int $winInBullets, \Domain\Entity\SeasonTeam $team)
	{
		$this->winInBullets[$team->getId()] = $winInBullets;
	}

	/**
	 * @param \Domain\Entity\SeasonTeam|null $team
	 * @return int
	 */
	public function getWinInOvertime(\Domain\Entity\SeasonTeam $team = null): int
	{
		if ($team === null) {
			return array_sum($this->winInOvertime);
		}
		return $this->winInOvertime[$team->getId()] ?? 0;
	}

	/**
	 * @param int $winInOvertime
	 * @param \Domain\Entity\SeasonTeam $team
	 */
	public function setWinInOvertime(int $winInOvertime, \Domain\Entity\SeasonTeam $team)
	{
		$this->winInOvertime[$team->getId()] = $winInOvertime;
	}

	/**
	 * @param \Domain\Entity\SeasonTeam|null $team
	 * @return int
	 */
	public function getLoseInMain(\Domain\Entity\SeasonTeam $team = null): int
	{
		if ($team === null) {
			return array_sum($this->loseInMain);
		}
		return $this->loseInMain[$team->getId()] ?? 0;
	}

	/**
	 * @param int $loseInMain
	 * @param \Domain\Entity\SeasonTeam $team
	 */
	public function setLoseInMain(int $loseInMain, \Domain\Entity\SeasonTeam $team)
	{
		$this->loseInMain[$team->getId()] = $loseInMain;
	}

	/**
	 * @param \Domain\Entity\SeasonTeam $team
	 * @return int
	 */
	public function getLoseInBullets(\Domain\Entity\SeasonTeam $team = null): int
	{
		if ($team === null) {
			return array_sum($this->loseInBullets);
		}
		return $this->loseInBullets[$team->getId()] ?? 0;
	}

	/**
	 * @param int $loseInBullets
	 * @param \Domain\Entity\SeasonTeam $team
	 */
	public function setLoseInBullets(int $loseInBullets, \Domain\Entity\SeasonTeam $team)
	{
		$this->loseInBullets[$team->getId()] = $loseInBullets;
	}

	/**
	 * @param \Domain\Entity\SeasonTeam|null $team
	 * @return int
	 */
	public function getLoseInOvertime(\Domain\Entity\SeasonTeam $team = null): int
	{
		if ($team === null) {
			return array_sum($this->loseInOvertime);
		}
		return $this->loseInOvertime[$team->getId()] ?? 0;
	}

	/**
	 * @param int $loseInOvertime
	 * @param \Domain\Entity\SeasonTeam $team
	 */
	public function setLoseInOvertime(int $loseInOvertime, \Domain\Entity\SeasonTeam $team)
	{
		$this->loseInOvertime[$team->getId()] = $loseInOvertime;
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
	 * @param \Domain\Entity\SeasonTeam|null $team
	 * @return int
	 */
	public function getScores(\Domain\Entity\SeasonTeam $team = null): int
	{
		return $this->getWinInMain($team) * 3
			+ ($this->getWinInBullets($team) + $this->getWinInOvertime($team)) * 2
			+ ($this->getLoseInBullets($team) + $this->getLoseInOvertime($team)) * 1;
	}

	/**
	 * @return \Domain\Entity\SeasonTeam
	 */
	public function getSeasonTeam(): \Domain\Entity\SeasonTeam
	{
		return $this->seasonTeam;
	}
}