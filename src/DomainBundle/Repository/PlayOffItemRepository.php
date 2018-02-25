<?php


namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\PlayOffItem;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\PlayOffItemRepositoryInterface;
use DomainBundle\Identity\PlayOffItemIdentity;

class PlayOffItemRepository extends EntityRepository implements PlayOffItemRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new PlayOffItemIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param PlayOffItem $item
	 */
	public function save(PlayOffItem $item)
	{
		$this->getEntityManager()->persist($item);
	}

	/**
	 * @param PlayOffItem $item
	 */
	public function remove(PlayOffItem $item)
	{
		$this->getEntityManager()->remove($item);
	}


	/**
	 * @return PlayOffItem
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): PlayOffItem
	{
		/** @var PlayOffItem $item */
		$item = $this->find($id);
		if (empty($item)) {
			throw new EntityNotFoundException('PlayOffItem not found');
		}
		return $item;
	}
}