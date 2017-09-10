<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:08
 */

namespace Domain\UseCase;


/**
 * Class RemoveTeamUseCase
 * @package Domain\UseCase
 */
class RemoveTeamUseCase
{
	use UseCaseTrait;

	/**
	 * @param int $teamId
	 */
	public function execute(int $teamId)
	{
		$team = $this->getContainer()->getTeamRepository()->findById($teamId);
		$seasonTeams = $this->getContainer()->getSeasonTeamRepository()->findByTeam($team);
		foreach ($seasonTeams as $seasonTeam) {
			(new RemoveSeasonTeamUseCase($this->getContainer()))->execute($seasonTeam->getId());

		}
		$this->getContainer()->getTeamRepository()->remove($team);
	}
}