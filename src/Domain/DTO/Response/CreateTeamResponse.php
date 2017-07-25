<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:32
 */

namespace Domain\DTO\Response;


use Domain\Entity\Team;

/**
 * Class CreateTeamResponse
 * @package Domain\DTO\Response
 */
class CreateTeamResponse
{
	private $team;

	/**
	 * CreateTeamResponse constructor.
	 * @param Team $team
	 */
	public function __construct(Team $team)
	{
		$this->team = $team;
	}

	/**
	 * @return Team
	 */
	public function getTeam(): Team
	{
		return $this->team;
	}
}