<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 0:28
 */

namespace Domain\Entity;


/**
 * Class GameEvent
 * @package Domain\Entity
 */
abstract class GameEvent implements \JsonSerializable
{
	const PERIOD_1 = 1;
	const PERIOD_2 = 2;
	const PERIOD_3 = 3;
	const PERIOD_OVERTIME = 4;
	const PERIOD_BULLETS = 5;
	/**
	 * @return string
	 */
	abstract function getType(): string;

	/**
	 * @return Game
	 */
	abstract public function getGame(): Game;

	/**
	 * @return int
	 */
	abstract public function getId(): int;

	/**
	 * @return int
	 */
	abstract public function getSecondsFromStart(): int;

	/**
	 * @return int
	 */
	abstract public function getPeriod(): int;
}