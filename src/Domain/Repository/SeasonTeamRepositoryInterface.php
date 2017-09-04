<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:45
 */

namespace Domain\Repository;


use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Entity\Team;
use Domain\Exception\EntityNotFoundException;

/**
 * Interface SeasonTeamRepositoryInterface
 * @package Domain\Repository
 */
interface SeasonTeamRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param Team $team
	 * @param Season $season
	 * @return SeasonTeam
	 * @throws EntityNotFoundException
	 */
	public function findByTeamAndSeason(Team $team, Season $season): SeasonTeam;

	/**
	 * @param SeasonTeam $seasonTeam
	 */
	public function save(SeasonTeam $seasonTeam);

	/**
	 * @param int $id
	 * @return SeasonTeam
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): SeasonTeam;

	/**
	 * @param Season $season
	 * @return SeasonTeam[]
	 */
	public function findBySeason(Season $season): array;

	/**
	 * @param SeasonTeam $seasonTeam
	 */
	public function remove(SeasonTeam $seasonTeam);
}