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
	 * @param $metadata
	 * @return Player
	 */
	public static function create($metadata = null): Player
	{
		$player = new Player();
		$player->setMetadata($metadata);
		return $player;
	}
}