<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:08
 */

namespace Domain\UseCase;


/**
 * Class RemoveSeasonTeamMembersUseCase
 * @package Domain\UseCase
 */
class RemoveSeasonTeamMembersUseCase
{
	use UseCaseTrait;

	/**
	 * @param int $seasonTeamId
	 */
	public function execute(int $seasonTeamId)
	{
		$seasonTeam = $this->getContainer()->getSeasonTeamRepository()->findById($seasonTeamId);
		$members = $this->getContainer()->getSeasonTeamMemberRepository()->findBySeasonTeam($seasonTeam);
		foreach ($members as $member) {
			$this->getContainer()->getSeasonTeamMemberRepository()->remove($member);
		}
	}
}