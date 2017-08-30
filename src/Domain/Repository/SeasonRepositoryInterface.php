<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:21
 */

namespace Domain\Repository;


use Domain\Entity\Season;
use Domain\Exception\EntityNotFoundException;

/**
 * Interface SeasonRepositoryInterface
 * @package Domain\Repository
 */
interface SeasonRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param int $id
	 * @return Season
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Season;

	/**
	 * @param int $year
	 * @return Season
	 * @throws EntityNotFoundException
	 */
	public function findByYear(int $year): Season;

	/**
	 * @param Season $season
	 */
	public function save(Season $season);

	/**
	 * @param Season $season
	 */
	public function remove(Season $season);
}