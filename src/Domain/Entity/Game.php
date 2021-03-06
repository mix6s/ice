<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.09.2017
 * Time: 20:08
 */

namespace Domain\Entity;

use Domain\Exception\DomainException;
use Domain\ValueObject\GameType;


/**
 * Class Game
 * @package Domain\Entity
 */
class Game implements \JsonSerializable
{
	use MetadataTrait;

	const STATE_DEFAULT = 0;
	const STATE_FINISHED = 1;

	private $id;
	private $datetime;
	private $type;
	private $place;
	private $season;
	private $seasonTeamA;
	private $seasonTeamB;
	private $state;
	private $membersA;
	private $membersB;
	private $playOffItem;
	private $scoreA = 0;
	private $scoreB = 0;

	/**
	 * Game constructor.
	 * @param int $id
	 */
	private function __construct()
	{

	}

	/**
	 * @param int $id
	 * @param \DateTime $datetime
	 * @param GameType $type
	 * @param string $place
	 * @param Season $season
	 * @param SeasonTeam $seasonTeamA
	 * @param SeasonTeam $seasonTeamB
	 * @return Game
	 */
	public static function create(
		int $id,
		\DateTime $datetime,
		GameType $type,
		string $place,
		Season $season,
		SeasonTeam $seasonTeamA,
		SeasonTeam $seasonTeamB
	) {
		$game = new self();
		$game->id = $id;
		$game->modify($datetime, $type, $place, $season, $seasonTeamA, $seasonTeamB, self::STATE_DEFAULT);
		return $game;
	}

	/**
	 * @param \DateTime $datetime
	 * @param GameType $type
	 * @param string $place
	 * @param Season $season
	 * @param SeasonTeam $seasonTeamA
	 * @param SeasonTeam $seasonTeamB
	 * @param int $state
	 * @throws DomainException
	 */
	public function modify(
		\DateTime $datetime,
		GameType $type,
		string $place,
		Season $season,
		SeasonTeam $seasonTeamA,
		SeasonTeam $seasonTeamB,
		int $state
	) {
		if ($season->getId() != $seasonTeamA->getSeason()->getId()
			|| $season->getId() != $seasonTeamB->getSeason()->getId()) {
			throw new DomainException("seasonteams seasons dont equal season");
		}

		if ($seasonTeamA->getId() === $seasonTeamB->getId()) {
			throw new DomainException("seasonteams equal");
		}
		$this->datetime = $datetime;
		$this->type = $type;
		$this->place = $place;
		$this->season = $season;
		$this->seasonTeamA = $seasonTeamA;
		$this->seasonTeamB = $seasonTeamB;
		$this->state = $state;
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'place' => $this->getPlace(),
			'scoreA' => $this->getScoreA(),
			'scoreB' => $this->getScoreB(),
			'datetime' => $this->getDatetime()->format('Y-m-d H:i'),
			'type' => (string)$this->getType(),
			'season' => $this->getSeason(),
			'seasonteamA' => $this->getSeasonTeamA(),
			'seasonteamB' => $this->getSeasonTeamB(),
			'winner' => $this->getWinnerSeasonTeam(),
			'metadata' => $this->getMetadata(),
			'state' => $this->getState(),
			'membersA' => $this->getMembersA(),
			'membersB' => $this->getMembersB(),
			'playoff_item' => $this->getPlayOffItem()
		];
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return \DateTime
	 */
	public function getDatetime(): \DateTime
	{
		return $this->datetime;
	}

	/**
	 * @return GameType
	 */
	public function getType(): GameType
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getPlace(): string
	{
		return $this->place;
	}

	/**
	 * @return Season
	 */
	public function getSeason(): Season
	{
		return $this->season;
	}

	/**
	 * @return SeasonTeam
	 */
	public function getSeasonTeamA(): SeasonTeam
	{
		return $this->seasonTeamA;
	}

	/**
	 * @return SeasonTeam
	 */
	public function getSeasonTeamB(): SeasonTeam
	{
		return $this->seasonTeamB;
	}

	/**
	 * @return int
	 */
	public function getState(): int
	{
		return $this->state;
	}

	/**
	 * @return mixed
	 */
	public function getMembersA(): array
	{
		if (!is_array($this->membersA)) {
			return [];
		}
		return $this->membersA;
	}

	/**
	 * @return mixed
	 */
	public function getMembersB(): array
	{
		if (!is_array($this->membersB)) {
			return [];
		}
		return $this->membersB;
	}

	/**
	 * @param array $membersA
	 */
	public function setMembersA(array $membersA)
	{
		$this->membersA = $membersA;
	}

	/**
	 * @param array $membersB
	 */
	public function setMembersB(array $membersB)
	{
		$this->membersB = $membersB;
	}

	/**
	 * @return PlayOffItem|null
	 */
	public function getPlayOffItem()
	{
		return $this->playOffItem;
	}

	/**
	 * @param PlayOffItem|null $playOffItem
	 */
	public function setPlayOffItem(PlayOffItem $playOffItem = null)
	{
		$this->playOffItem = $playOffItem;
	}

	/**
	 * @return int
	 */
	public function getScoreA(): int
	{
		return $this->scoreA;
	}

	/**
	 * @param int $scoreA
	 */
	public function setScoreA(int $scoreA)
	{
		$this->scoreA = $scoreA;
	}

	/**
	 * @return SeasonTeam|null
	 */
	public function getWinnerSeasonTeam()
	{
		if ($this->getState() !== self::STATE_FINISHED) {
			return null;
		}
		return $this->scoreA > $this->scoreB ? $this->getSeasonTeamA() : $this->getSeasonTeamB();
	}
	/**
	 * @return int
	 */
	public function getScoreB(): int
	{
		return $this->scoreB;
	}

	/**
	 * @param int $scoreB
	 */
	public function setScoreB(int $scoreB)
	{
		$this->scoreB = $scoreB;
	}
}