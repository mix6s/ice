<?php


namespace Domain\DTO\Response;


use Domain\Entity\PlayOff;

class CreatePlayOffResponse
{
	/**
	 * @var PlayOff
	 */
	private $playOff;

	public function __construct(PlayOff $playOff)
	{
		$this->playOff = $playOff;
	}

	public function getPlayOff(): PlayOff
	{
		return $this->playOff;
	}
}