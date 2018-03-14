<?php


namespace Domain\Entity;


class PlayOff implements \JsonSerializable
{
	/**
	 * @var int
	 */
	private $id;
	/**
	 * @var Season
	 */
	private $season;
	/**
	 * @var League
	 */
	private $league;
	/**
	 * @var \DateTime
	 */
	private $startAt;

	public function __construct(int $id, Season $season, League $league, \DateTime $startAt)
	{
		$this->id = $id;
		$this->season = $season;
		$this->league = $league;
		$this->startAt = $startAt;
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
			'season' => $this->season,
			'league' => $this->league,
			'start_at' => $this->startAt->format('Y-m-d H:i')
		];
	}

	public function isStarted(): bool
	{
		return time() > $this->startAt->getTimestamp();
	}

	/**
	 * @param \DateTime $startAt
	 */
	public function setStartAt(\DateTime $startAt)
	{
		$this->startAt = $startAt;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return League
	 */
	public function getLeague(): League
	{
		return $this->league;
	}
}