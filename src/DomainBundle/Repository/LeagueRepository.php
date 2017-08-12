<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\League;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\LeagueRepositoryInterface;
use DomainBundle\Identity\LeagueIdentity;

/**
 * Class TeamRepository
 * @package DomainBundle\Repository
 */
class LeagueRepository extends EntityRepository implements LeagueRepositoryInterface
{

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new LeagueIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param int $id
	 * @return League
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): League
	{
		/** @var League $league */
		$league = $this->find($id);
		if (empty($league)) {
			throw new EntityNotFoundException(sprintf('League with id %d not found', $id));
		}
		return $league;
	}

	/**
	 * @param League $league
	 */
	public function save(League $league)
	{
		$this->getEntityManager()->persist($league);
	}
}