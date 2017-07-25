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
	 * @return mixed
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @param mixed $metadata
	 */
	public function setMetadata($metadata)
	{
		$this->metadata = $metadata;
	}

}