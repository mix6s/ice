<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 22:06
 */

namespace Domain\DTO\Response;


use Domain\Entity\Season;

/**
 * Class CreateSeasonResponse
 * @package Domain\DTO\Response
 */
class CreateSeasonResponse
{
	private $season;

	/**
	 * CreateSeasonResponse constructor.
	 * @param Season $season
	 */
	public function __construct(Season $season)
	{
		$this->season = $season;
	}

	/**
	 * @return Season
	 */
	public function getSeason(): Season
	{
		return $this->season;
	}
}