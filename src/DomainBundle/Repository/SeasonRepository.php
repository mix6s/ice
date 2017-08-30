<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\Season;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\SeasonRepositoryInterface;
use DomainBundle\Identity\SeasonIdentity;

/**
 * Class SeasonRepository
 * @package DomainBundle\Repository
 */
class SeasonRepository extends EntityRepository implements SeasonRepositoryInterface
{

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new SeasonIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param int $id
	 * @return Season
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Season
	{
		/** @var Season $season */
		$season = $this->find($id);
		if (empty($season)) {
			throw new EntityNotFoundException(sprintf('Season with id %d not found', $id));
		}
		return $season;
	}

	/**
	 * @param Season $season
	 */
	public function save(Season $season)
	{
		$this->getEntityManager()->persist($season);
	}

	/**
	 * @param int $year
	 * @return Season
	 * @throws EntityNotFoundException
	 */
	public function findByYear(int $year): Season
	{
		/** @var Season $season */
		$season = $this->findOneBy([
			'year' => $year
		]);
		if (empty($season)) {
			throw new EntityNotFoundException(sprintf('Season with year %d not found', $year));
		}
		return $season;
	}

	/**
	 * @param Season $season
	 */
	public function remove(Season $season)
	{
		$this->getEntityManager()->remove($season);
	}
}