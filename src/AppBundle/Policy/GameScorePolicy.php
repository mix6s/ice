<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.09.2017
 * Time: 22:52
 */

namespace AppBundle\Policy;


use Domain\Entity\Game;
use Domain\Entity\GoalEvent;
use DomainBundle\Repository\GameEventRepository;

/**
 * Class GameScorePolicy
 * @package AppBundle\Policy
 */
class GameScorePolicy
{
	private $scoresByGame = [];
	private $gameEventRepository;

	/**
	 * GameScorePolicy constructor.
	 * @param GameEventRepository $gameEventRepository
	 */
	public function __construct(GameEventRepository $gameEventRepository)
	{
		$this->gameEventRepository = $gameEventRepository;
	}

	/**
	 * @param Game $game
	 * @return int|null
	 */
	public function scoreA(Game $game)
	{
		return $this->getScores($game)[0];
	}

	/**
	 * @param Game $game
	 * @return int|null
	 */
	public function scoreB(Game $game)
	{
		return $this->getScores($game)[1];
	}

	/**
	 * @param Game $game
	 * @return array
	 */
	private function getScores(Game $game)
	{
		if ($game->getDatetime()->getTimestamp() > (new \DateTime())->getTimestamp()) {
			return [null, null];
		}
		if (array_key_exists($game->getId(), $this->scoresByGame)) {
			return $this->scoresByGame[$game->getId()];
		}

		$events = $this->gameEventRepository->findByGame($game);
		$scoreA = 0;
		$scoreB = 0;
		foreach ($events as $event) {
			if ($event instanceof GoalEvent) {
				if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
					$scoreA++;
				} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
					$scoreB++;
				}
			}
		}
		$this->scoresByGame[$game->getId()] = [$scoreA, $scoreB];
		return $this->scoresByGame[$game->getId()];
	}
}