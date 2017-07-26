<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:49
 */

namespace Domain\Entity;


/**
 * Class Player
 * @package Domain\Entity
 */
class Player
{
	use MetadataTrait;

	private $id;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * Player constructor.
	 * @param int $id
	 */
	private function __construct(int $id)
	{
		$this->id = $id;
	}

	/**
	 * @param $metadata
	 * @param int $id
	 * @return Player
	 */
	public static function create(int $id, $metadata = null): Player
	{
		$player = new Player($id);
		$player->setMetadata($metadata);
		return $player;
	}
}