<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 18.09.2017
 * Time: 21:41
 */

namespace Domain\UseCase;


use Domain\Entity\Game;
use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\GameType;

/**
 * Class SaveGameUseCase
 * @package Domain\UseCase
 */
class SaveGameUseCase
{
	use UseCaseTrait;

	/**
	 * @param null $id
	 * @param $type
	 * @param $place
	 * @param $datetime
	 * @param $seasonId
	 * @param $seasonteamAId
	 * @param $seasonteamBId
	 * @param $state
	 * @return Game
	 */
	public function execute($id = null, $type, $place, $datetime, $seasonId, $seasonteamAId, $seasonteamBId, $state, $membersA = [], $membersB = [], $playOffItemId)
	{
		$season = $this->getContainer()->getSeasonRepository()->findById($seasonId);
		$seasonteamA = $this->getContainer()->getSeasonTeamRepository()->findById($seasonteamAId);
		$seasonteamB = $this->getContainer()->getSeasonTeamRepository()->findById($seasonteamBId);

		try {
			$game = $this->getContainer()->getGameRepository()->findById((int)$id);
			$game->modify(new \DateTime($datetime), GameType::resolve($type), $place, $season, $seasonteamA, $seasonteamB, $state);
		} catch (EntityNotFoundException $e) {
			$id = $this->getContainer()->getGameRepository()->getNextId();
			$game = Game::create($id, new \DateTime($datetime), GameType::resolve($type), $place, $season, $seasonteamA, $seasonteamB);
		}
		$game->setMembersA($membersA);
		$game->setMembersB($membersB);
		if ($playOffItemId !== null) {
			$playOff = $this->getContainer()->getPlayOffItemRepository()->findById($playOffItemId);
			$game->setPlayOffItem($playOff);
		}
		$this->getContainer()->getGameRepository()->save($game);
		return $game;
	}
}