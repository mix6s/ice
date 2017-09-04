<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:42
 */

namespace Domain\DTO\Request;


/**
 * Class CreateSeasonTeamRequest
 * @package Domain\DTO\Request
 */
class CreateSeasonTeamRequest
{
	private $coachId;
	private $seasonId;
	private $leagueId;
	private $teamId;

	/**
	 * CreateSeasonTeamRequest constructor.
	 * @param int $teamId
	 * @param int $coachId
	 * @param int $seasonId
	 * @param int $leagueId
	 */
	public function __construct(int $teamId, int $coachId, int $seasonId, int $leagueId)
	{
		$this->teamId = $teamId;
		$this->coachId = $coachId;
		$this->seasonId = $seasonId;
		$this->leagueId = $leagueId;
	}

	/**
	 * @return int
	 */
	public function getCoachId(): int
	{
		return $this->coachId;
	}

	/**
	 * @return int
	 */
	public function getSeasonId(): int
	{
		return $this->seasonId;
	}

	/**
	 * @return int
	 */
	public function getLeagueId(): int
	{
		return $this->leagueId;
	}

	/**
	 * @return int
	 */
	public function getTeamId(): int
	{
		return $this->teamId;
	}

	/**
	 * @return array
	 */
	public function getMembers(): array
	{
		return $this->members;
	}
}