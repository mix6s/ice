<?php


namespace Domain\DTO\Request;


class SavePlayOffItemRequest
{
	/**
	 * @var int
	 */
	private $rank;
	/**
	 * @var int
	 */
	private $playoffId;
	/**
	 * @var int|null
	 */
	private $seasonteamAId;
	/**
	 * @var int|null
	 */
	private $seasonteamBId;
	/**
	 * @var int|null
	 */
	private $winnerId;
	/**
	 * @var null
	 */
	private $id;

	public function __construct(int $playoffId, int $rank, $id = null)
	{
		$this->playoffId = $playoffId;
		$this->rank = $rank;
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getRank(): int
	{
		return $this->rank;
	}

	/**
	 * @return int
	 */
	public function getPlayoffId(): int
	{
		return $this->playoffId;
	}

	/**
	 * @return int|null
	 */
	public function getSeasonteamAId()
	{
		return $this->seasonteamAId;
	}

	/**
	 * @return int|null
	 */
	public function getSeasonteamBId()
	{
		return $this->seasonteamBId;
	}

	/**
	 * @return int|null
	 */
	public function getWinnerId()
	{
		return $this->winnerId;
	}

	/**
	 * @param int $rank
	 */
	public function setRank(int $rank)
	{
		$this->rank = $rank;
	}

	/**
	 * @param int $playoffId
	 */
	public function setPlayoffId(int $playoffId)
	{
		$this->playoffId = $playoffId;
	}

	/**
	 * @param int|null $seasonteamAId
	 */
	public function setSeasonteamAId($seasonteamAId)
	{
		if (empty($seasonteamAId)) {
			$this->seasonteamAId = null;
			return;
		}
		$this->seasonteamAId = (int)$seasonteamAId;
	}

	/**
	 * @param int|null $seasonteamBId
	 */
	public function setSeasonteamBId($seasonteamBId)
	{
		if (empty($seasonteamBId)) {
			$this->seasonteamBId = null;
			return;
		}
		$this->seasonteamBId = (int)$seasonteamBId;
	}

	/**
	 * @param int|null $winnerId
	 */
	public function setWinnerId($winnerId)
	{
		if (empty($winnerId)) {
			$this->winnerId = null;
			return;
		}
		$this->winnerId = (int)$winnerId;
	}

	/**
	 * @return null
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