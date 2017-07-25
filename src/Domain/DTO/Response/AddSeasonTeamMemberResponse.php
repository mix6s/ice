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
	private $member;

	/**
	 * AddSeasonTeamMemberResponse constructor.
	 * @param SeasonTeamMember $member
	 */
	public function __construct(SeasonTeamMember $member)
	{
		$this->member = $member;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getMember(): SeasonTeamMember
	{
		return $this->member;
	}
}