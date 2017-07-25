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
	 * @param $metadata
	 * @return League
	 */
	public static function create($metadata = null): League
	{
		$league = new League();
		$league->setMetadata($metadata);
		return $league;
	}
}