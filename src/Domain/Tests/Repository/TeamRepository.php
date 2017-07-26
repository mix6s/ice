<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 26.07.2017
 * Time: 8:23
 */

namespace Domain\Tests\Repository;


use Domain\Entity\Team;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\TeamRepositoryInterface;

/**
 * Class TeamRepository
 * @package Domain\Tests\Repository
 */
class TeamRepository implements TeamRepositoryInterface
{
	use InMemoryRepositoryTrait;

	/**
	 * @param int $id
	 * @return Team
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Team
	{
		if (!isset($this->store[$id])) {
			throw new EntityNotFoundException();
		}
		return $this->store[$id];
	}

	/**
	 * @param Team $team
	 */
	public function save(Team $team)
	{
		$this->storeEntity($team, $team->getId());
	}
}