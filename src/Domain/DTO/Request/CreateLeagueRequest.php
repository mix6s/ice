<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:31
 */

namespace Domain\DTO\Request;


/**
 * Class CreateLeagueRequest
 * @package Domain\DTO\Request
 */
class CreateLeagueRequest
{
	private $metadata;

	/**
	 * CreateLeagueRequest constructor.
	 * @param $metadata
	 */
	public function __construct($metadata)
	{
		$this->metadata = $metadata;
	}

	/**
	 * @return mixed
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}
}