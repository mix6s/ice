<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.09.2017
 * Time: 22:52
 */

namespace AppBundle\Policy;


use AppBundle\Statistic\Aggregator;
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
	private $aggregator;

	/**
	 * GameScorePolicy constructor.
	 * @param GameEventRepository $gameEventRepository
	 */
	public function __construct(GameEventRepository $gameEventRepository, Aggregator $aggregator)
	{
		$this->gameEventRepository = $gameEventRepository;
		$this->aggregator = $aggregator;
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
		if (count($events) === 0 && $game->getState() !== Game::STATE_FINISHED) {
			return [null, null];
		}
		$gameStatistic = $this->aggregator->getGameStatistic($game);
		$this->scoresByGame[$game->getId()] = [$gameStatistic->getTeamAGoals(), $gameStatistic->getTeamBGoals()];
		return $this->scoresByGame[$game->getId()];
	}
}