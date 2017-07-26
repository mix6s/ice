<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 26.07.2017
 * Time: 8:10
 */

namespace Domain\Tests;


use Domain\ContainerInterface;
use Domain\Repository\LeagueRepositoryInterface;
use Domain\Repository\PlayerRepositoryInterface;
use Domain\Repository\SeasonRepositoryInterface;
use Domain\Repository\SeasonTeamMemberRepositoryInterface;
use Domain\Repository\SeasonTeamRepository;
use Domain\Repository\TeamRepositoryInterface;
use Domain\Tests\Repository\TeamRepository;

/**
 * Class Container
 * @package Domain\Tests
 */
class Container implements ContainerInterface
{
	private $teamRepository;

	/**
	 * @return SeasonRepositoryInterface
	 */
	public function getSeasonRepository(): SeasonRepositoryInterface
	{
		// TODO: Implement getSeasonRepository() method.
	}

	/**
	 * @return TeamRepositoryInterface
	 */
	public function getTeamRepository(): TeamRepositoryInterface
	{
		if (!$this->teamRepository) {
			$this->teamRepository = new TeamRepository();
		}
		return $this->teamRepository;
	}

	/**
	 * @return SeasonTeamRepository
	 */
	public function getSeasonTeamRepository(): SeasonTeamRepository
	{

	}

	/**
	 * @return PlayerRepositoryInterface
	 */
	public function getPlayerRepository(): PlayerRepositoryInterface
	{
		// TODO: Implement getPlayerRepository() method.
	}

	/**
	 * @return LeagueRepositoryInterface
	 */
	public function getLeagueRepository(): LeagueRepositoryInterface
	{
		// TODO: Implement getLeagueRepository() method.
	}

	/**
	 * @return SeasonTeamMemberRepositoryInterface
	 */
	public function getSeasonTeamMemberRepository(): SeasonTeamMemberRepositoryInterface
	{
		// TODO: Implement getSeasonTeamMemberRepository() method.
	}
}