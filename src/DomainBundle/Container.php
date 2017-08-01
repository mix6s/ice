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
use Domain\Repository\SeasonTeamRepositoryInterface;
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
		return $this->container->get('domain.repository.season');
	}

	/**
	 * @return TeamRepositoryInterface
	 */
	public function getTeamRepository(): TeamRepositoryInterface
	{
		return $this->container->get('domain.repository.team');
	}

	/**
	 * @return SeasonTeamRepositoryInterface
	 */
	public function getSeasonTeamRepository(): SeasonTeamRepositoryInterface
	{
		// TODO: Implement getSeasonTeamRepository() method.
	}

	/**
	 * @return PlayerRepositoryInterface
	 */
	public function getPlayerRepository(): PlayerRepositoryInterface
	{
		return $this->container->get('domain.repository.player');
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