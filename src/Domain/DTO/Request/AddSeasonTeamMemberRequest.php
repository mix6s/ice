<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:21
 */

namespace Domain\DTO\Request;


/**
 * Class AddSeasonTeamMemberRequest
 * @package Domain\DTO\Request
 */
class AddSeasonTeamMemberRequest
{
	private $coachId;
	private $seasonTeamId;
	private $playerId;
	private $type;

	/**
	 * AddSeasonTeamMemberRequest constructor.
	 * @param int $coachId
	 * @param int $seasonTeamId
	 * @param int $playerId
	 * @param string $type
	 */
	public function __construct(int $coachId, int $seasonTeamId, int $playerId, string $type)
	{
		$this->coachId = $coachId;
		$this->seasonTeamId = $seasonTeamId;
		$this->playerId = $playerId;
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getCoachId(): int
	{
		return $this->coachId;
	}

	/**
	 * @return int
	 */
	public function getSeasonTeamId(): int
	{
		return $this->seasonTeamId;
	}

	/**
	 * @return int
	 */
	public function getPlayerId(): int
	{
		return $this->playerId;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}
}