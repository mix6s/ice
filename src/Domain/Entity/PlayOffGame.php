<?php


namespace Domain\Entity;


class PlayOffGame implements \JsonSerializable
{

	/**
	 * @var int
	 */
	private $id;
	/**
	 * @var PlayOffGridItem
	 */
	private $item;
	/**
	 * @var Game
	 */
	private $game;
	/**
	 * @var SeasonTeam|null
	 */
	private $winner;

	public function __construct(int $id, PlayOffGridItem $item, Game $game, SeasonTeam $winner = null)
	{
		$this->id = $id;
		$this->item = $item;
		$this->game = $game;
		$this->winner = $winner;
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return [
			'id' => $this->id,
			'item' => $this->item,
			'game' => $this->game,
			'winner' => $this->winner
		];
	}

	/**
	 * @param SeasonTeam $winner
	 */
	public function setWinner(SeasonTeam $winner)
	{
		$this->winner = $winner;
	}
}