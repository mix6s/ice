<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\Game;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\GameRepositoryInterface;
use DomainBundle\Identity\GameIdentity;

/**
 * Class GameRepository
 * @package DomainBundle\Repository
 */
class GameRepository extends EntityRepository implements GameRepositoryInterface
{
	const DEFAULT_LIMIT = 20;

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new GameIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param int $id
	 * @return Game
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Game
	{
		/** @var Game $game */
		$game = $this->find($id);
		if (empty($game)) {
			throw new EntityNotFoundException(sprintf('Game with id %d not found', $id));
		}
		return $game;
	}

	/**
	 * @param Game $game
	 */
	public function save(Game $game)
	{
		$this->getEntityManager()->persist($game);
	}

	/**
	 * @param Game $game
	 */
	public function remove(Game $game)
	{
		$this->getEntityManager()->remove($game);
	}
}