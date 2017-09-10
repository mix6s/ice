<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:08
 */

namespace Domain\UseCase;


/**
 * Class RemoveLeagueUseCase
 * @package Domain\UseCase
 */
class RemoveLeagueUseCase
{
	use UseCaseTrait;

	/**
	 * @param int $leagueId
	 */
	public function execute(int $leagueId)
	{
		$league = $this->getContainer()->getLeagueRepository()->findById($leagueId);
		$seasonTeams = $this->getContainer()->getSeasonTeamRepository()->findByLeague($league);
		foreach ($seasonTeams as $seasonTeam) {
			(new RemoveSeasonTeamUseCase($this->getContainer()))->execute($seasonTeam->getId());

		}
		$this->getContainer()->getLeagueRepository()->remove($league);
	}
}