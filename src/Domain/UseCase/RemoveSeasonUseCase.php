<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:08
 */

namespace Domain\UseCase;


/**
 * Class RemoveSeasonUseCase
 * @package Domain\UseCase
 */
class RemoveSeasonUseCase
{
	use UseCaseTrait;

	/**
	 * @param int $seasonId
	 */
	public function execute(int $seasonId)
	{
		$season = $this->getContainer()->getSeasonRepository()->findById($seasonId);
		$seasonTeams = $this->getContainer()->getSeasonTeamRepository()->findBySeason($season);
		foreach ($seasonTeams as $seasonTeam) {
			(new RemoveSeasonTeamUseCase($this->getContainer()))->execute($seasonTeam->getId());

		}
		$this->getContainer()->getSeasonRepository()->remove($season);
	}
}