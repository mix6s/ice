<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:29
 */

namespace Domain\Repository;


use Domain\Entity\Team;
use Domain\Exception\EntityNotFoundException;

/**
 * Interface TeamRepositoryInterface
 * @package Domain\Repository
 */
interface TeamRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param int $id
	 * @return Team
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Team;

	/**
	 * @param Team $team
	 */
	public function save(Team $team);
}