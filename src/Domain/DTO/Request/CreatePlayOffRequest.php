<?php


namespace Domain\DTO\Request;


class CreatePlayOffRequest
{
	/** @var  int */
	private $seasonId;
	/** @var  int */
	private $leagueId;
	/** @var  \DateTime */
	private $startAt;

	public function __construct(int $seasonId, int $leagueId, \DateTime $startAt)
	{
		$this->seasonId = $seasonId;
		$this->leagueId = $leagueId;
		$this->startAt = $startAt;
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


}