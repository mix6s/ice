<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Domain\Entity\SeasonTeam;
use Domain\Entity\Team;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\TeamRepositoryInterface;
use DomainBundle\Identity\TeamIdentity;

/**
 * Class TeamRepository
 * @package DomainBundle\Repository
 */
class TeamRepository extends EntityRepository implements TeamRepositoryInterface
{
	const DEFAULT_LIMIT = 20;

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new TeamIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param int $id
	 * @return Team
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Team
	{
		/** @var Team $team */
		$team = $this->find($id);
		if (empty($team)) {
			throw new EntityNotFoundException(sprintf('Team with id %d not found', $id));
		}
		return $team;
	}

	/**
	 * @param Team $team
	 */
	public function save(Team $team)
	{
		$this->getEntityManager()->persist($team);
	}

	/**
	 * @param Team $team
	 */
	public function remove(Team $team)
	{
		$this->getEntityManager()->remove($team);
	}
}