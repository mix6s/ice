<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:16
 */

namespace Domain\Repository;


use Domain\Entity\Player;
use Domain\Exception\EntityNotFoundException;

/**
 * Interface PlayerRepositoryInterface
 * @package Domain\Repository
 */
interface PlayerRepositoryInterface
{
	/**
	 * @param int $id
	 * @return Player
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Player;
}