<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:32
 */

namespace Domain\DTO\Response;


use Domain\Entity\League;

/**
 * Class CreateLeagueResponse
 * @package Domain\DTO\Response
 */
class CreateLeagueResponse
{
	private $league;

	/**
	 * CreateLeagueResponse constructor.
	 * @param League $league
	 */
	public function __construct(League $league)
	{
		$this->league = $league;
	}

	/**
	 * @return League
	 */
	public function getLeague(): League
	{
		return $this->league;
	}
}