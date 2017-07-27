<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:40
 */

namespace DomainBundle;


use Domain\ContainerInterface;
use Domain\Repository\LeagueRepositoryInterface;
use Domain\Repository\PlayerRepositoryInterface;
use Domain\Repository\SeasonRepositoryInterface;
use Domain\Repository\SeasonTeamMemberRepositoryInterface;
use Domain\Repository\SeasonTeamRepository;
use Domain\Repository\TeamRepositoryInterface;

class Container implements ContainerInterface
{
	private $container;

	public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @return SeasonRepositoryInterface
	 */
	public function getSeasonRepository(): SeasonRepositoryInterface
	{

	}

	/**
	 * @return TeamRepositoryInterface
	 */
	public function getTeamRepository(): TeamRepositoryInterface
	{
		return $this->container->get('domain.repository.team');
	}

	/**
	 * @return SeasonTeamRepository
	 */
	public function getSeasonTeamRepository(): SeasonTeamRepository
	{
		// TODO: Implement getSeasonTeamRepository() method.
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