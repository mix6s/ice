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
	 * @param $metadata
	 * @return Team
	 */
	public static function create($metadata = null): Team
	{
		$team = new Team();
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