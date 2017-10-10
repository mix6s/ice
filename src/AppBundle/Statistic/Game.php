<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 10.10.2017
 * Time: 20:55
 */

namespace AppBundle\Statistic;


class Game
{
	private $teamABullets = 0;
	private $teamBBullets = 0;
	private $teamAPenaltyTime = 0;
	private $teamBPenaltyTime = 0;

	/**
	 * @return int
	 */
	public function getTeamABullets(): int
	{
		return $this->teamABullets;
	}

	/**
	 * @param int $teamABullets
	 */
	public function setTeamABullets(int $teamABullets)
	{
		$this->teamABullets = $teamABullets;
	}

	/**
	 * @return int
	 */
	public function getTeamBBullets(): int
	{
		return $this->teamBBullets;
	}

	/**
	 * @param int $teamBBullets
	 */
	public function setTeamBBullets(int $teamBBullets)
	{
		$this->teamBBullets = $teamBBullets;
	}

	/**
	 * @return int
	 */
	public function getTeamAPenaltyTime(): int
	{
		return $this->teamAPenaltyTime;
	}

	/**
	 * @param int $teamAPenaltyTime
	 */
	public function setTeamAPenaltyTime(int $teamAPenaltyTime)
	{
		$this->teamAPenaltyTime = $teamAPenaltyTime;
	}

	/**
	 * @return int
	 */
	public function getTeamBPenaltyTime(): int
	{
		return $this->teamBPenaltyTime;
	}

	/**
	 * @param int $teamBPenaltyTime
	 */
	public function setTeamBPenaltyTime(int $teamBPenaltyTime)
	{
		$this->teamBPenaltyTime = $teamBPenaltyTime;
	}
}