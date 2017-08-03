<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:29
 */

namespace Domain\Entity;


/**
 * Trait MetadataTrait
 * @package Domain\Entity
 */
trait MetadataTrait
{
	private $metadata;

	/**
	 * @return \JsonSerializable
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @param \JsonSerializable|null $metadata
	 */
	public function setMetadata(\JsonSerializable $metadata = null)
	{
		$this->metadata = $metadata;
	}

}