<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:14
 */

namespace Domain\Entity;


/**
 * Class SeasonTeamMember
 * @package Domain\Entity
 */
class SeasonTeamMember
{
	const TYPE_CAPTAIN = 'captain';
	const TYPE_ASSISTANT = 'assistant';
	const TYPE_DEFAULT = 'default';

	private $id;
	private $player;
	private $type;
	private $seasonTeam;

	/**
	 * SeasonTeamMember constructor.
	 * @param int $id
	 * @param SeasonTeam $seasonTeam
	 * @param Player $player
	 * @param string $type
	 */
	private function __construct(int $id, SeasonTeam $seasonTeam, Player $player, string $type = self::TYPE_DEFAULT)
	{
		$this->id = $id;
		$this->seasonTeam = $seasonTeam;
		$this->player = $player;
		$this->type = $type;
	}

	/**
	 * @param int $id
	 * @param SeasonTeam $seasonTeam
	 * @param Player $player
	 * @param string $type
	 * @return SeasonTeamMember
	 */
	public static function create(int $id, SeasonTeam $seasonTeam, Player $player, string $type = self::TYPE_DEFAULT): SeasonTeamMember
	{
		return new SeasonTeamMember($id, $seasonTeam, $player, $type);
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player
	{
		return $this->player;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return SeasonTeam
	 */
	public function getSeasonTeam(): SeasonTeam
	{
		return $this->seasonTeam;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}
}