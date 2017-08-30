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
class SeasonTeamMember implements \JsonSerializable
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
			'seasonteam' => $this->getSeasonTeam(),
			'type' => $this->getType(),
			'player' => $this->getPlayer(),
		];
	}
}