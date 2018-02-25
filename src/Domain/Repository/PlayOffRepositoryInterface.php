<?php


namespace Domain\Repository;


use Domain\Entity\PlayOff;
use Domain\Exception\EntityNotFoundException;

interface PlayOffRepositoryInterface
{
	/**
	 * @return PlayOff
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): PlayOff;

	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param PlayOff $playoff
	 */
	public function save(PlayOff $playoff);

	/**
	 * @param PlayOff $playoff
	 */
	public function remove(PlayOff $playoff);

	/**
	 * @param int $seasonId
	 * @param int $leagueId
	 * @return PlayOff
	 * @throws EntityNotFoundException
	 */
	public function findBySeasonAndLeague(int $seasonId, int $leagueId): PlayOff;
}