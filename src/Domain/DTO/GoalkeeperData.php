<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:28
 */

namespace Domain\DTO;


/**
 * Class GoalkeeperData
 * @package Domain\DTO
 */
class GoalkeeperData
{
	private $goals;
	private $time;
	private $bullets;
	private $memberId;

	/**
	 * GoalkeeperData constructor.
	 * @param int $goals
	 * @param int $bullets
	 * @param string $time
	 * @param int $memberId
	 */
	public function __construct(int $goals, int $bullets, string $time, int $memberId)
	{
		$this->goals = $goals;
		$this->memberId = $memberId;
		$this->bullets = $bullets;
		$this->time = $time;
	}

	/**
	 * @return int
	 */
	public function getMemberId(): int
	{
		return $this->memberId;
	}

	/**
	 * @return int
	 */
	public function getDuration(): int
	{
		$seconds = 0;
		$parts = explode(':', $this->time);
		if (isset($parts[0])) {
			$seconds += isset($parts[1]) ? $parts[0] * 60 : $parts[0];
		}

		if (isset($parts[1])) {
			$seconds += $parts[1];
		}
		return $seconds;
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
	public function getBullets(): int
	{
		return $this->bullets;
	}
}