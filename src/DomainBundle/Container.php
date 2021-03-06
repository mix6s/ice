<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:40
 */

namespace DomainBundle;


use Domain\ContainerInterface;
use Domain\Repository\GameEventRepositoryInterface;
use Domain\Repository\GameRepositoryInterface;
use Domain\Repository\LeagueRepositoryInterface;
use Domain\Repository\PlayerRepositoryInterface;
use Domain\Repository\PlayOffItemRepositoryInterface;
use Domain\Repository\PlayOffRepositoryInterface;
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
		return $this->container->get('domain.repository.seasonteam');
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
		return $this->container->get('domain.repository.league');
	}

	/**
	 * @return SeasonTeamMemberRepositoryInterface
	 */
	public function getSeasonTeamMemberRepository(): SeasonTeamMemberRepositoryInterface
	{
		return $this->container->get('domain.repository.seasonteammember');
	}

	/**
	 * @return GameRepositoryInterface
	 */
	public function getGameRepository(): GameRepositoryInterface
	{
		return $this->container->get('domain.repository.game');
	}

	/**
	 * @return GameEventRepositoryInterface
	 */
	public function getGameEventRepository(): GameEventRepositoryInterface
	{
		return $this->container->get('domain.repository.game.events');
	}

	/**
	 * @return PlayOffRepositoryInterface
	 */
	public function getPlayOffRepository(): PlayOffRepositoryInterface
	{
		return $this->container->get('domain.repository.playoff');
	}

	public function getPlayOffItemRepository(): PlayOffItemRepositoryInterface
	{
		return $this->container->get('domain.repository.playoffitem');
	}
}