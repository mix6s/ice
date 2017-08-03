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
class Player implements \JsonSerializable
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
	 * @param \JsonSerializable $metadata
	 * @param int $id
	 * @return Player
	 */
	public static function create(int $id, \JsonSerializable $metadata = null): Player
	{
		$player = new Player($id);
		$player->setMetadata($metadata);
		return $player;
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'metadata' => $this->getMetadata()
		];
	}
}