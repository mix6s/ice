<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 21:54
 */

namespace Domain\DTO\Response;


use Domain\Entity\SeasonTeamMember;

/**
 * Class AddSeasonTeamMemberResponse
 * @package Domain\DTO\Response
 */
class AddSeasonTeamMemberResponse
{
	private $members;

	/**
	 * AddSeasonTeamMemberResponse constructor.
	 * @param SeasonTeamMember[] $members
	 */
	public function __construct(array $members)
	{
		$this->members = $members;
	}

	/**
	 * @return SeasonTeamMember[]
	 */
	public function getMembers(): array
	{
		return $this->members;
	}
}