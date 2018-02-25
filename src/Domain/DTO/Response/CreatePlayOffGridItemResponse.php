<?php


namespace Domain\DTO\Response;


use Domain\Entity\PlayOffItem;

class CreatePlayOffGridItemResponse
{
	/**
	 * @var PlayOffItem
	 */
	private $playOffItem;

	public function __construct(PlayOffItem $playOffItem)
	{
		$this->playOffItem = $playOffItem;
	}

	public function getItem(): PlayOffItem
	{
		return $this->playOffItem;
	}
}