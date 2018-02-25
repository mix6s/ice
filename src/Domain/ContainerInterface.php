<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:33
 */

namespace Domain;


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

/**
 * Interface ContainerInterface
 * @package Domain
 */
interface ContainerInterface
{
	/**
	 * @return SeasonRepositoryInterface
	 */
	public function getSeasonRepository(): SeasonRepositoryInterface;

	/**
	 * @return TeamRepositoryInterface
	 */
	public function getTeamRepository(): TeamRepositoryInterface;

	/**
	 * @return SeasonTeamRepositoryInterface
	 */
	public function getSeasonTeamRepository(): SeasonTeamRepositoryInterface;

	/**
	 * @return PlayerRepositoryInterface
	 */
	public function getPlayerRepository(): PlayerRepositoryInterface;

	/**
	 * @return LeagueRepositoryInterface
	 */
	public function getLeagueRepository(): LeagueRepositoryInterface;

	/**
	 * @return SeasonTeamMemberRepositoryInterface
	 */
	public function getSeasonTeamMemberRepository(): SeasonTeamMemberRepositoryInterface;

	/**
	 * @return GameRepositoryInterface
	 */
	public function getGameRepository(): GameRepositoryInterface;

	/**
	 * @return GameEventRepositoryInterface
	 */
	public function getGameEventRepository(): GameEventRepositoryInterface;

	/**
	 * @return PlayOffRepositoryInterface
	 */
	public function getPlayOffRepository(): PlayOffRepositoryInterface;

	public function getPlayOffItemRepository(): PlayOffItemRepositoryInterface;
}