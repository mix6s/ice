<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:05
 */

namespace Domain\DTO\Response;


use Domain\Entity\SeasonTeam;

/**
 * Class CreateSeasonTeamResponse
 * @package Domain\DTO\Response
 */
class CreateSeasonTeamResponse
{
	private $seasonTeam;

	/**
	 * CreateSeasonTeamResponse constructor.
	 * @param SeasonTeam $seasonTeam
	 */
	public function __construct(SeasonTeam $seasonTeam)
	{
		$this->seasonTeam = $seasonTeam;
	}

	/**
	 * @return SeasonTeam
	 */
	public function getSeasonTeam(): SeasonTeam
	{
		return $this->seasonTeam;
	}
}