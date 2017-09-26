<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:08
 */

namespace Domain\UseCase;


/**
 * Class RemoveSeasonTeamUseCase
 * @package Domain\UseCase
 */
class RemoveSeasonTeamUseCase
{
	use UseCaseTrait;

	/**
	 * @param int $seasonTeamId
	 */
	public function execute(int $seasonTeamId)
	{
		$seasonTeam = $this->getContainer()->getSeasonTeamRepository()->findById($seasonTeamId);
		$games = $this->getContainer()->getGameRepository()->findBySeasonTeam($seasonTeam);
		$removeGameUseCase = new RemoveGameUseCase($this->getContainer());
		foreach ($games as $game) {
			$removeGameUseCase->execute($game->getId());
		}

		(new RemoveSeasonTeamMembersUseCase($this->getContainer()))->execute($seasonTeam->getId());
		$this->getContainer()->getSeasonTeamRepository()->remove($seasonTeam);
	}
}