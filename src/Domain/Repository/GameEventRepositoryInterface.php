<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:49
 */

namespace Domain\Repository;


use Domain\Entity\Game;
use Domain\Entity\GameEvent;
use Domain\Exception\EntityNotFoundException;

/**
 * Interface GameEventRepositoryInterface
 * @package Domain\Repository
 */
interface GameEventRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	/**
	 * @param GameEvent $event
	 */
	public function save(GameEvent $event);

	/**
	 * @param GameEvent $event
	 */
	public function remove(GameEvent $event);

	/**
	 * @param int $id
	 * @return GameEvent
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): GameEvent;

	/**
	 * @param Game $game
	 * @return GameEvent[]
	 */
	public function findByGame(Game $game): array;
}