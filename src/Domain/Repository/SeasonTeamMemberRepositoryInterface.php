<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 21:44
 */

namespace Domain\Repository;


use Domain\Entity\Player;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Entity\SeasonTeamMember;
use Domain\Exception\EntityNotFoundException;

/**
 * Interface SeasonTeamMemberRepositoryInterface
 * @package Domain\Repository
 */
interface SeasonTeamMemberRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param Player $player
	 * @param Season $season
	 * @return SeasonTeamMember
	 * @throws EntityNotFoundException
	 */
	public function findByPlayerAndSeason(Player $player, Season $season): SeasonTeamMember;

	/**
	 * @param SeasonTeamMember $member
	 */
	public function save(SeasonTeamMember $member);

	/**
	 * @param SeasonTeamMember $member
	 */
	public function remove(SeasonTeamMember $member);

	/**
	 * @param int $id
	 * @return SeasonTeamMember
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): SeasonTeamMember;

	/**
	 * @param SeasonTeam $seasonTeam
	 * @return SeasonTeamMember[]
	 */
	public function findBySeasonTeam(SeasonTeam $seasonTeam): array;
}