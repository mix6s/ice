<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\Player;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\PlayerRepositoryInterface;
use DomainBundle\Identity\PlayerIdentity;

/**
 * Class PlayerRepository
 * @package DomainBundle\Repository
 */
class PlayerRepository extends EntityRepository implements PlayerRepositoryInterface
{

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new PlayerIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param int $id
	 * @return Player
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Player
	{
		/** @var Player $player */
		$player = $this->find($id);
		if (empty($player)) {
			throw new EntityNotFoundException(sprintf('Player with id %d not found', $id));
		}
		return $player;
	}

	/**
	 * @param int $limit
	 * @param int $offset
	 * @return Player[]
	 */
	public function findPlayers(string $query, int $limit, int $offset)
	{
		$builder = $this->createQueryBuilder('p')
			->join('p.metadata', 'm')
			->setMaxResults($limit)
			->setFirstResult($offset)
			->orderBy('p.id', 'DESC');
		if (!empty($query)) {
			$builder
				->where('m.surname like :query')
				->orWhere('m.firstName like :query')
				->orWhere('m.secondName like :query')
				->setParameter('query', '%' . $query . '%');
		}
		return $builder
			->getQuery()
			->getResult();
	}

	/**
	 * @return mixed
	 */
	public function countPlayers(string $query)
	{
		$builder = $this->createQueryBuilder('p')
			->select('count(p.id)')
			->join('p.metadata', 'm');

		if (!empty($query)) {
			$builder
				->where('m.surname like :query')
				->orWhere('m.firstName like :query')
				->orWhere('m.secondName like :query')
				->setParameter('query', '%' . $query . '%');
		}
		return $builder
			->getQuery()
			->getSingleScalarResult();
	}
}