<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:32
 */

namespace Domain\DTO\Response;


use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;

/**
 * Class CopySeasonResponse
 * @package Domain\DTO\Response
 */
class CopySeasonResponse
{
	private $season;
	private $seasonTeams;

	/**
	 * CopySeasonResponse constructor.
	 * @param Season $season
	 * @param SeasonTeam[] $seasonTeams
	 */
	public function __construct(Season $season, array $seasonTeams)
	{
		$this->season = $season;
		$this->seasonTeams = $seasonTeams;
	}

	/**
	 * @return Season
	 */
	public function getSeason(): Season
	{
		return $this->season;
	}

	/**
	 * @return SeasonTeam[]
	 */
	public function getSeasonTeams(): array
	{
		return $this->seasonTeams;
	}
}