<?php


namespace Domain\DTO\Request;


class SavePlayOffRequest
{
	/** @var  int */
	private $seasonId;
	/** @var  int */
	private $leagueId;
	/** @var  \DateTime */
	private $startAt;
	/**
	 * @var int
	 */
	private $id;

	public function __construct(int $seasonId, int $leagueId, \DateTime $startAt, $id = null)
	{
		$this->seasonId = $seasonId;
		$this->leagueId = $leagueId;
		$this->startAt = $startAt;
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getSeasonId(): int
	{
		return $this->seasonId;
	}

	/**
	 * @return int
	 */
	public function getLeagueId(): int
	{
		return $this->leagueId;
	}

	/**
	 * @return \DateTime
	 */
	public function getStartAt(): \DateTime
	{
		return $this->startAt;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function hasId(): bool
	{
		return !empty($this->id);
	}
}