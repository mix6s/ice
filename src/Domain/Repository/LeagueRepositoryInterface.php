<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:15
 */

namespace Domain\Repository;


use Domain\Entity\League;
use Domain\Exception\EntityNotFoundException;

/**
 * Class LeagueRepositoryInterface
 * @package Domain\Repository
 */
interface LeagueRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param int $id
	 * @return League
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): League;

	/**
	 * @param League $league
	 */
	public function save(League $league);
}