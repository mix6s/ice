<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 24.09.2017
 * Time: 20:16
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityManager;
use Domain\Entity\Game;
use Domain\Entity\GameEvent;
use Domain\Entity\GoalEvent;
use Domain\Entity\GoalkeeperEvent;
use Domain\Entity\PenaltyEvent;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\GameEventRepositoryInterface;
use DomainBundle\CacheTrait;
use DomainBundle\Identity\GameEventIdentity;

/**
 * Class GameEventRepository
 * @package DomainBundle\Repository
 */
class GameEventRepository implements GameEventRepositoryInterface
{
	use CacheTrait;

	private $em;

	/**
	 * GameEventRepository constructor.
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * @return EntityManager
	 */
	private function getEntityManager()
	{
		return $this->em;
	}

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new GameEventIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * /**
	 * @param GameEvent $event
	 */
	public function save(GameEvent $event)
	{
		$this->getEntityManager()->persist($event);
		switch ($event->getType()) {
			case 'goalkeeper':
				/** @var GoalkeeperEvent $event */
				$this->getCache()->invalidateTags(['member.' . $event->getMember()->getId()]);
				break;
			case 'goal':
				/** @var GoalEvent $event */
				$this->getCache()->invalidateTags(['member.' . $event->getMember()->getId()]);
				if ($event->getAssistantA() !== null) {
					$this->getCache()->invalidateTags(['member.' . $event->getAssistantA()->getId()]);
				}
				if ($event->getAssistantB() !== null) {
					$this->getCache()->invalidateTags(['member.' . $event->getAssistantB()->getId()]);
				}
				break;
			case 'penalty':
				/** @var PenaltyEvent $event */
				$this->getCache()->invalidateTags(['member.' . $event->getMember()->getId()]);
				break;
			default:
				break;
		}
		$this->getCache()->invalidateTags([
			'game.' . $event->getGame()->getId(),
			'season.' . $event->getGame()->getSeason()->getId(),
		]);
	}

	/**
	 * @param GameEvent $event
	 */
	public function remove(GameEvent $event)
	{
		$this->getEntityManager()->remove($event);
	}

	/**
	 * @param int $id
	 * @return GameEvent
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): GameEvent
	{
		/** @var GameEvent $game */
		$event = $this->getEntityManager()->createQueryBuilder()
			->from('Domain:GoalEvent', 'e')
			->where('e.id = :id')
			->setParameter('id', $id)->getQuery()
			->getSingleResult();

		if (empty($event)) {
			$event = $this->getEntityManager()->createQueryBuilder()
				->from('Domain:PenaltyEvent', 'e')
				->where('id = :id')
				->setParameter('id', $id)->getQuery()
				->getSingleResult();
		}

		if (empty($event)) {
			$event = $this->getEntityManager()->createQueryBuilder()
				->from('Domain:GoalkeeperEvent', 'e')
				->where('id = :id')
				->setParameter('id', $id)->getQuery()
				->getSingleResult();
		}

		if (empty($event)) {
			throw new EntityNotFoundException(sprintf('Game event with id %d not found', $id));
		}
		return $event;
	}

	/**
	 * @param Game $game
	 * @return GameEvent[]
	 */
	public function findByGame(Game $game): array
	{
		$events = $this->getEntityManager()->createQueryBuilder()
			->select('e')
			->from('Domain:GoalEvent', 'e')
			->where('e.game = :gameId')
			->orderBy('e.secondsFromStart')
			->setParameter('gameId', $game->getId())->getQuery()
			->getResult();

		$penaltyEvent = $this->getEntityManager()->createQueryBuilder()
			->select('e')
			->from('Domain:PenaltyEvent', 'e')
			->where('e.game = :gameId')
			->orderBy('e.secondsFromStart')
			->setParameter('gameId', $game->getId())->getQuery()
			->getResult();

		$keeperEvent = $this->getEntityManager()->createQueryBuilder()
			->select('e')
			->from('Domain:GoalkeeperEvent', 'e')
			->where('e.game = :gameId')
			->setParameter('gameId', $game->getId())->getQuery()
			->getResult();

		foreach ($penaltyEvent as $event) {
			$events[] = $event;
		}
		usort($events, function (GameEvent $a, GameEvent $b) {
			if ($a->getSecondsFromStart() === $b->getSecondsFromStart()) {
				return 0;
			}
			return ($a->getSecondsFromStart() < $b->getSecondsFromStart()) ? -1 : 1;
		});
		foreach ($keeperEvent as $event) {
			$events[] = $event;
		}
		return $events;
	}
}