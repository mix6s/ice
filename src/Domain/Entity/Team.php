<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:22
 */

namespace Domain\Entity;

/**
 * Class Team
 * @package Domain\Entity
 */
class Team implements \JsonSerializable
{
	use MetadataTrait;

	private $id;

	/**
	 * Team constructor.
	 * @param int $id
	 */
	private function __construct(int $id)
	{
		$this->id = $id;
	}

	/**
	 * @param int $id
	 * @param $metadata
	 * @return Team
	 */
	public static function create(int $id, \JsonSerializable $metadata = null): Team
	{
		$team = new Team($id);
		$team->setMetadata($metadata);
		return $team;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
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