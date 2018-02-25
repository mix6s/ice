<?php


namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\PlayOff;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\PlayOffRepositoryInterface;
use DomainBundle\Identity\PlayOffIdentity;

class PlayOffRepository extends EntityRepository implements PlayOffRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new PlayOffIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param PlayOff $playoff
	 */
	public function save(PlayOff $playoff)
	{
		$this->getEntityManager()->persist($playoff);
	}

	/**
	 * @param PlayOff $playoff
	 */
	public function remove(PlayOff $playoff)
	{
		$this->getEntityManager()->remove($playoff);
	}

	/**
	 * @param int $seasonId
	 * @param int $leagueId
	 * @return PlayOff
	 * @throws EntityNotFoundException
	 */
	public function findBySeasonAndLeague(int $seasonId, int $leagueId): PlayOff
	{
		/** @var PlayOff $playoff */
		$playoff = $this->findOneBy([
			'season' => $seasonId,
			'league' => $leagueId
		]);
		if (empty($playoff)) {
			throw new EntityNotFoundException('PlayOff not found');
		}
		return $playoff;
	}

	/**
	 * @return PlayOff
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): PlayOff
	{
		/** @var PlayOff $playoff */
		$playoff = $this->find($id);
		if (empty($playoff)) {
			throw new EntityNotFoundException('PlayOff not found');
		}
		return $playoff;
	}
}