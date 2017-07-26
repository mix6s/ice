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
class Team
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
	 * @param $metadata
	 * @return Team
	 */
	public static function create(int $id, $metadata = null): Team
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
}