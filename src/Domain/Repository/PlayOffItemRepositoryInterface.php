<?php


namespace Domain\Repository;


use Domain\Entity\PlayOffItem;

interface PlayOffItemRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param PlayOffItem $item
	 */
	public function save(PlayOffItem $item);

	/**
	 * @param PlayOffItem $item
	 */
	public function remove(PlayOffItem $item);

	public function findById(int $playOffItemId): PlayOffItem;
}