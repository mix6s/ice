<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:49
 */

namespace Domain\Entity;


/**
 * Class League
 * @package Domain\Entity
 */
class League implements \JsonSerializable
{
	use MetadataTrait;

	private $id;

	/**
	 * League constructor.
	 * @param int $id
	 */
	private function __construct(int $id)
	{
		$this->id = $id;
	}

	/**
	 * @param \JsonSerializable $metadata
	 * @param int $id
	 * @return League
	 */
	public static function create(int $id, \JsonSerializable $metadata = null): League
	{
		$league = new League($id);
		$league->setMetadata($metadata);
		return $league;
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