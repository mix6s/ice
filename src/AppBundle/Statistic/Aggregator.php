<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 10.10.2017
 * Time: 20:21
 */

namespace AppBundle\Statistic;


use AppBundle\Policy\GameScorePolicy;
use Domain\Entity\Game;
use Domain\Entity\GoalEvent;
use Domain\Entity\GoalkeeperEvent;
use Domain\Entity\PenaltyEvent;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeamMember;
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
	 * @param SeasonTeamMember $member
	 * @return \AppBundle\Statistic\SeasonTeamMember
	 */
	public function getSeasonTeamMemberStatistic(SeasonTeamMember $member): \AppBundle\Statistic\SeasonTeamMember
	{
		if (array_key_exists($member->getId(), $this->seasonTeamMembers)) {
			return $this->seasonTeamMembers[$member->getId()];
		}

		$this->seasonTeamMembers[$member->getId()] = $this->cache->getItem('stat.member.' . $member->getId())->get();
		if (!empty($this->seasonTeamMembers[$member->getId()])) {
			return $this->seasonTeamMembers[$member->getId()];
		}

		$stat = new \AppBundle\Statistic\SeasonTeamMember();
		$games = $this->gameRepository->findBySeasonTeam($member->getSeasonTeam());
		foreach ($games as $game) {
			if (!in_array($member->getId(), $game->getMembersA()) && !in_array($member->getId(), $game->getMembersB())) {
				continue;
			}

			if ($game->getState() !== Game::STATE_FINISHED) {
				continue;
			}
			$stat->setGamesCount($stat->getGamesCount() + 1);
			$events = $this->gameEventRepository->findByGame($game);
			foreach ($events as $event) {
				switch ($event->getType()) {
					case 'goalkeeper':
						/** @var GoalkeeperEvent $event */
						$stat->setGoalsFailed($stat->getGoalsFailed() + $event->getGoals());
						$stat->setTotalSecondsTime($stat->getTotalSecondsTime() + $event->getDuration());
						break;
					case 'goal':
						/** @var GoalEvent $event */
						$assistants = [];
						if ($event->getAssistantA()) {
							$assistants[] = $event->getAssistantA()->getId();
						}
						if ($event->getAssistantB()) {
							$assistants[] = $event->getAssistantB()->getId();
						}
						if ($event->getMember()->getId() === $member->getId()) {
							$stat->setGoals($stat->getGoals() + 1);
						} elseif (in_array($member->getId(), $assistants)) {
							$stat->setAssistantGoals($stat->getAssistantGoals() + 1);
						}
						break;
					case 'penalty':
						/** @var PenaltyEvent $event */
						if ($event->getMember()->getId() === $member->getId()) {
							$stat->setPenaltyTime($stat->getPenaltyTime() + $this->getPenaltyTime($event->getPenaltyTimeType()));
						}
						break;
					default:
						break;
				}
			}
			if ($stat->getGoalsFailed() === 0) {
				$stat->setZeroGameCount($stat->getZeroGameCount() + 1);
			}
		}
		$this->seasonTeamMembers[$member->getId()] = $stat;
		$cached = $this->cache->getItem('stat.member.' . $member->getId());
		$cached->tag(['member.' . $member->getId()]);
		$cached->set($stat);
		$this->cache->save($cached);
		return $this->seasonTeamMembers[$member->getId()];
	}

	/**
	 * @param Game $game
	 * @return \AppBundle\Statistic\Game
	 */
	public function getGameStatistic(Game $game): \AppBundle\Statistic\Game
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
			switch ($event->getType()) {
				case 'goalkeeper':
					/** @var GoalkeeperEvent $event */
					if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
						$stat->setTeamABullets($stat->getTeamABullets() + $event->getBullets());
					} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
						$stat->setTeamBBullets($stat->getTeamBBullets() + $event->getBullets());
					}
					break;
				case 'goal':
					/** @var GoalEvent $event */
					if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
						$stat->setTeamAGoals($stat->getTeamAGoals() + 1);
					} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
						$stat->setTeamBGoals($stat->getTeamBGoals() + 1);
					}
					break;
				case 'penalty':
					/** @var PenaltyEvent $event */
					$time = $this->getPenaltyTime($event->getPenaltyTimeType());
					if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
						$stat->setTeamAPenaltyTime($stat->getTeamAPenaltyTime() + $time);
					} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
						$stat->setTeamBPenaltyTime($stat->getTeamBPenaltyTime() + $time);
					}
					break;
				default:
					break;
			}
		}
		$this->games[$game->getId()] = $stat;
		$cached = $this->cache->getItem('stat.game.' . $game->getId());
		$cached->tag(['game.' . $game->getId()]);
		$cached->set($stat);
		$this->cache->save($cached);
		return $this->games[$game->getId()];
	}

	/**
	 * @param string $type
	 * @return int
	 */
	private function getPenaltyTime(string $type): int
	{
		switch ($type) {
			case PenaltyEvent::PENALTY_TIME_TYPE_2:
				return 2;
			case PenaltyEvent::PENALTY_TIME_TYPE_5_20:
				return 20;
			case PenaltyEvent::PENALTY_TIME_TYPE_2_2:
				return 4;
			default:
				return 0;
		}
	}
}