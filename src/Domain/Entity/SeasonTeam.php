<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:54
 */

namespace Domain\Entity;


/**
 * Class SeasonTeam
 * @package Domain\Entity
 */
class SeasonTeam
{
	private $id;
	private $team;
	private $league;
	private $season;
	private $coach;

	/**
	 * SeasonTeam constructor.
	 * @param Team $team
	 * @param League $league
	 * @param Season $season
	 * @param Player $coach
	 */
	private function __construct(Team $team, League $league, Season $season, Player $coach)
	{
		$this->team = $team;
		$this->league = $league;
		$this->season = $season;
		$this->coach = $coach;
	}

	/**
	 * @param Team $team
	 * @param League $league
	 * @param Season $season
	 * @param Player $coach
	 * @return SeasonTeam
	 */
	public static function create(Team $team, League $league, Season $season, Player $coach): SeasonTeam
	{
		return new SeasonTeam($team, $league, $season, $coach);
	}

	/**
	 * @return Team
	 */
	public function getTeam(): Team
	{
		return $this->team;
	}

	/**
	 * @return Player
	 */
	public function getCoach(): Player
	{
		return $this->coach;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return Season
	 */
	public function getSeason(): Season
	{
		return $this->season;
	}
}