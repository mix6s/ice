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
class League
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
	 * @param $metadata
	 * @param int $id
	 * @return League
	 */
	public static function create(int $id, $metadata = null): League
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
}