<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 10.10.2017
 * Time: 20:21
 */

namespace AppBundle\Statistic;


use Domain\Entity\Game;
use Domain\Entity\GoalkeeperEvent;
use Domain\Entity\PenaltyEvent;
use Domain\Entity\Season;
use DomainBundle\Repository\GameEventRepository;
use DomainBundle\Repository\GameRepository;
use DomainBundle\Repository\SeasonTeamMemberRepository;
use DomainBundle\Repository\SeasonTeamRepository;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class Aggregator
 * @package AppBundle\Statistic
 */
class Aggregator
{
	private $gameRepository;
	private $seasonTeamRepository;
	private $seasonTeamMemberRepository;
	private $gameEventRepository;
	private $cache;

	private $games = [];
	private $seasonTeams = [];
	private $seasonTeamMembers = [];

	public function __construct(
		AdapterInterface $cache,
		GameRepository $gameRepository,
		SeasonTeamRepository $seasonTeamRepository,
		SeasonTeamMemberRepository $seasonTeamMemberRepository,
		GameEventRepository $gameEventRepository
	)
	{
		$this->cache = $cache;
		$this->gameRepository = $gameRepository;
		$this->seasonTeamRepository = $seasonTeamRepository;
		$this->seasonTeamMemberRepository = $seasonTeamMemberRepository;
		$this->gameEventRepository = $gameEventRepository;
	}

	/**
	 * @param Game $game
	 * @return \AppBundle\Statistic\Game
	 */
	public function getGameStatistic(Game $game)
	{
		if (array_key_exists($game->getId(), $this->games)) {
			return $this->games[$game->getId()];
		}

		$this->games[$game->getId()] = $this->cache->getItem('stat.game.' . $game->getId())->get();
		if (!empty($this->games[$game->getId()])) {
			return $this->games[$game->getId()];
		}

		$events = $this->gameEventRepository->findByGame($game);
		$stat = new \AppBundle\Statistic\Game();
		foreach ($events as $event) {
			if ($event instanceof GoalkeeperEvent) {
				if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
					$stat->setTeamABullets($stat->getTeamABullets() + $event->getBullets());
				} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
					$stat->setTeamBBullets($stat->getTeamBBullets() + $event->getBullets());
				}
			} elseif ($event instanceof PenaltyEvent) {
				$time = 0;
				switch ($event->getPenaltyTimeType()) {
					case PenaltyEvent::PENALTY_TIME_TYPE_2:
						$time = 2;
						break;
					case PenaltyEvent::PENALTY_TIME_TYPE_5_20:
						$time = 20;
						break;
					case PenaltyEvent::PENALTY_TIME_TYPE_2_2:
						$time = 4;
						break;
					default:
						break;
				}
				if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
					$stat->setTeamAPenaltyTime($stat->getTeamAPenaltyTime() + $time);
				} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
					$stat->setTeamBPenaltyTime($stat->getTeamBPenaltyTime() + $time);
				}
			}
			continue;
		}
		$this->games[$game->getId()] = $stat;
		$cached = $this->cache->getItem('stat.game.' . $game->getId());
		$cached->tag(['game.' . $game->getId()]);
		$cached->set($stat);
		$this->cache->save($cached);
		return $this->games[$game->getId()];
	}
}