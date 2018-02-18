<?php


namespace Domain\DTO\Request;


class UpdatePlayOffGridItemRequest
{
	/**
	 * @var int
	 */
	private $gameId;

	public function __construct(int $gameId)
	{
		$this->gameId = $gameId;
	}

	/**
	 * @return int
	 */
	public function getGameId(): int
	{
		return $this->gameId;
	}
}