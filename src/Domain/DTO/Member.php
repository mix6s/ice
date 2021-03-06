<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 30.08.2017
 * Time: 21:05
 */

namespace Domain\DTO;


/**
 * Class Member
 * @package Domain\DTO
 */
class Member
{
	private $playerId;
	private $type;
	private $number;
	private $id;

	/**
	 * Member constructor.
	 * @param int $id
	 * @param int $playerId
	 * @param string $type
	 * @param int $number
	 */
	public function __construct(int $id = null, int $playerId, string $type, int $number)
	{
		$this->playerId = $playerId;
		$this->type = $type;
		$this->number = $number;
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getPlayerId(): int
	{
		return $this->playerId;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return int
	 */
	public function getNumber(): int
	{
		return $this->number;
	}

	/**
	 * @return int|null
	 */
	public function getId()
	{
		return $this->id;
	}
}