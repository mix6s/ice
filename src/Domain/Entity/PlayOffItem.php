<?php


namespace Domain\Entity;


class PlayOffItem implements \JsonSerializable
{
	/**
	 * @var int
	 */
	private $id;
	/**
	 * @var PlayOff
	 */
	private $playOff;
	/**
	 * @var int
	 */
	private $rank;
	/**
	 * @var SeasonTeam|null
	 */
	private $seasonTeamA;
	/**
	 * @var SeasonTeam|null
	 */
	private $seasonTeamB;
	/**
	 * @var SeasonTeam|null
	 */
	private $winner;

	public function __construct(
		int $id,
		PlayOff $playOff,
		int $rank,
		SeasonTeam $seasonTeamA = null,
		SeasonTeam $seasonTeamB = null,
		SeasonTeam $winner = null
	) {
		$this->id = $id;
		$this->playOff = $playOff;
		$this->rank = $rank;
		$this->seasonTeamA = $seasonTeamA;
		$this->seasonTeamB = $seasonTeamB;
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
			'playoff' => $this->playOff,
			'rank' => $this->rank,
			'seasonteamA' => $this->seasonTeamA,
			'seasonteamB' => $this->seasonTeamB,
			'winner' => $this->winner
		];
	}

	/**
	 * @param SeasonTeam $seasonTeamA
	 */
	public function setSeasonTeamA(SeasonTeam $seasonTeamA)
	{
		$this->seasonTeamA = $seasonTeamA;
	}

	/**
	 * @param SeasonTeam $seasonTeamB
	 */
	public function setSeasonTeamB(SeasonTeam $seasonTeamB)
	{
		$this->seasonTeamB = $seasonTeamB;
	}

	/**
	 * @param SeasonTeam $winner
	 */
	public function setWinner(SeasonTeam $winner)
	{
		$this->winner = $winner;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return PlayOff
	 */
	public function getPlayOff(): PlayOff
	{
		return $this->playOff;
	}

	/**
	 * @return int
	 */
	public function getRank(): int
	{
		return $this->rank;
	}

	/**
	 * @return SeasonTeam|null
	 */
	public function getSeasonTeamA()
	{
		return $this->seasonTeamA;
	}

	/**
	 * @return SeasonTeam|null
	 */
	public function getSeasonTeamB()
	{
		return $this->seasonTeamB;
	}

	/**
	 * @return SeasonTeam|null
	 */
	public function getWinner()
	{
		return $this->winner;
	}
}