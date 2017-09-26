<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:21
 */

namespace Domain\DTO\Request;

use Domain\DTO\Member;


/**
 * Class AddSeasonTeamMemberRequest
 * @package Domain\DTO\Request
 */
class AddSeasonTeamMemberRequest
{
	private $coachId;
	private $seasonTeamId;
	private $members = [];

	/**
	 * AddSeasonTeamMemberRequest constructor.
	 * @param int $coachId
	 * @param int $seasonTeamId
	 */
	public function __construct(int $coachId, int $seasonTeamId)
	{
		$this->coachId = $coachId;
		$this->seasonTeamId = $seasonTeamId;
		$this->members = [];
	}

	/**
	 * @param int $playerId
	 * @param string $type
	 * @param int $number
	 */
	public function addMember(int $playerId, string $type, int $number)
	{
		$this->members[] = new Member($playerId, $type, $number);
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
	 * @return Member[]
	 */
	public function getMembers(): array
	{
		return $this->members;
	}
}