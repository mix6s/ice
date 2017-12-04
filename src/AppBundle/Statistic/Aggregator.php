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
use Domain\Entity\GameEvent;
use Domain\Entity\GoalEvent;
use Domain\Entity\GoalkeeperEvent;
use Domain\Entity\League;
use Domain\Entity\PenaltyEvent;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Entity\SeasonTeamMember;
use DomainBundle\Entity\PlayerMetadata;
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

	private $games = null;
	private $seasonTeams = [];
	private $seasonTeamMembers = [];
	private $seasons = [];
	private $top = [];

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
	 * @param Season $season
	 * @return array
	 */
	public function getTopStatistic(Season $season)
	{
		return;
	}

	/**
	 * @param Season $season
	 * @return \AppBundle\Statistic\Season
	 */
	public function getSeasonStatistic(Season $season): \AppBundle\Statistic\Season
	{
		if (array_key_exists($season->getId(), $this->seasons)) {
			return $this->seasons[$season->getId()];
		}

		$this->seasons[$season->getId()] = $this->cache->getItem('stat.season.' . $season->getId())->get();
		if (!empty($this->seasons[$season->getId()])) {
			return $this->seasons[$season->getId()];
		}

		$stat = new \AppBundle\Statistic\Season($season);
		$teams = $this->seasonTeamRepository->findBySeason($season);
		foreach ($teams as $seasonTeam) {
			$seasonTeamStat = $this->getSeasonTeamStatistic($seasonTeam);
			$membersStat = $seasonTeamStat->getMembersStatistic();
			$leagueBests = $stat->getBestsByLeague($seasonTeam->getLeague());
			foreach ($membersStat as $memberStat)
			{
				$leagueBests->nominate($memberStat);
				$leagueBests->addMember($memberStat);
			}
			$stat->setSeasonTeamStatistic($seasonTeamStat);
			$stat->setBestsByLeague($leagueBests);
		}
		$this->seasons[$season->getId()] = $stat;
		$cached = $this->cache->getItem('stat.season.' . $season->getId());
		$cached->tag(['season.' . $season->getId()]);
		$cached->set($stat);
		$this->cache->save($cached);
		return $this->seasons[$season->getId()];
	}

	/**
	 * @param SeasonTeam $seasonTeam
	 * @return \AppBundle\Statistic\SeasonTeam
	 */
	public function getSeasonTeamStatistic(SeasonTeam $seasonTeam): \AppBundle\Statistic\SeasonTeam
	{
		if (array_key_exists($seasonTeam->getId(), $this->seasonTeams)) {
			return $this->seasonTeams[$seasonTeam->getId()];
		}

		$this->seasonTeams[$seasonTeam->getId()] = $this->cache->getItem('stat.seasonteam.' . $seasonTeam->getId())->get();
		if (!empty($this->seasonTeams[$seasonTeam->getId()])) {
			return $this->seasonTeams[$seasonTeam->getId()];
		}

		$stat = new \AppBundle\Statistic\SeasonTeam($seasonTeam);
		$games = $this->gameRepository->findBySeasonTeam($seasonTeam);
		$seasonTeamMembers = $this->seasonTeamMemberRepository->findBySeasonTeam($seasonTeam);
		foreach ($games as $game) {
			if ($game->getState() !== Game::STATE_FINISHED) {
				continue;
			}
			$gameStat = $this->getGameStatistic($game);

			$stat->setGamesCount($stat->getGamesCount() + 1);

			$lastEventPeriod = GameEvent::PERIOD_1;
			$oppositeTeam = $game->getSeasonTeamA()->getId() === $seasonTeam->getId() ? $game->getSeasonTeamB() : $game->getSeasonTeamA();
			$events = $this->gameEventRepository->findByGame($game);
			foreach ($events as $event) {
				switch ($event->getType()) {
					case 'goalkeeper':
						/** @var GoalkeeperEvent $event */
						break;
					case 'goal':
						/** @var GoalEvent $event */
						if ($event->getMember()->getSeasonTeam()->getId() === $seasonTeam->getId()) {
							$stat->setGoals($stat->getGoals($oppositeTeam) + 1, $oppositeTeam);
						} else {
							$stat->setGoalsFailed($stat->getGoalsFailed($oppositeTeam) + 1, $oppositeTeam);
						}
						$lastEventPeriod = $event->getPeriod();
						break;
					case 'penalty':
						/** @var PenaltyEvent $event */
						$lastEventPeriod = $event->getPeriod();
						break;
					default:
						break;
				}
				foreach ($seasonTeamMembers as $member) {
					$memberStat = $stat->getMemberStatistic($member);
					$memberStat->aggregate($game, $event);
					$stat->setMemberStatistic($memberStat);
				}
			}
			$isWinner = false;
			if ($game->getSeasonTeamA()->getId() === $seasonTeam->getId() && $gameStat->getTeamAGoals() > $gameStat->getTeamBGoals()
				|| $game->getSeasonTeamB()->getId() === $seasonTeam->getId() && $gameStat->getTeamAGoals() < $gameStat->getTeamBGoals()) {
				$isWinner = true;
			}
			if ($isWinner) {
				switch ($lastEventPeriod) {
					case GameEvent::PERIOD_1:
					case GameEvent::PERIOD_2:
					case GameEvent::PERIOD_3:
						$stat->setWinInMain($stat->getWinInMain($oppositeTeam) + 1, $oppositeTeam);
						break;
					case GameEvent::PERIOD_OVERTIME:
						$stat->setWinInOvertime($stat->getWinInOvertime($oppositeTeam) + 1, $oppositeTeam);
						break;
					case GameEvent::PERIOD_BULLETS:
						$stat->setWinInBullets($stat->getWinInBullets($oppositeTeam) + 1, $oppositeTeam);
						break;
					default:
						break;
				}
			} else {
				switch ($lastEventPeriod) {
					case GameEvent::PERIOD_1:
					case GameEvent::PERIOD_2:
					case GameEvent::PERIOD_3:
						$stat->setLoseInMain($stat->getLoseInMain($oppositeTeam) + 1, $oppositeTeam);
						break;
					case GameEvent::PERIOD_OVERTIME:
						$stat->setLoseInOvertime($stat->getLoseInOvertime($oppositeTeam) + 1, $oppositeTeam);
						break;
					case GameEvent::PERIOD_BULLETS:
						$stat->setLoseInBullets($stat->getLoseInBullets($oppositeTeam) + 1, $oppositeTeam);
						break;
					default:
						break;
				}
			}
		}

		$this->seasonTeams[$seasonTeam->getId()] = $stat;
		$cached = $this->cache->getItem('stat.seasonteam.' . $seasonTeam->getId());
		$cached->tag(['seasonteam.' . $seasonTeam->getId(), 'season.' . $seasonTeam->getSeason()->getId()]);
		$cached->set($stat);
		$this->cache->save($cached);
		return $this->seasonTeams[$seasonTeam->getId()];
	}
	/**
	 * @param SeasonTeamMember $member
	 * @return \AppBundle\Statistic\SeasonTeamMember
	 */
	public function getSeasonTeamMemberStatistic(SeasonTeamMember $member): \AppBundle\Statistic\SeasonTeamMember
	{
		return $this->getSeasonTeamStatistic($member->getSeasonTeam())->getMemberStatistic($member);
	}

	/**
	 * @param Game $game
	 * @return \AppBundle\Statistic\Game
	 */
	public function getGameStatistic(Game $game): \AppBundle\Statistic\Game
	{
		if (empty($this->games)) {
			$this->games = $this->cache->getItem('stat.games')->get();
		}

		if (!is_array($this->games)) {
			$this->games = [];
		}

		if (array_key_exists($game->getId(), $this->games)) {
			return $this->games[$game->getId()];
		}


		$events = $this->gameEventRepository->findByGame($game);
		$stat = new \AppBundle\Statistic\Game();
		$lastEventPeriod = GameEvent::PERIOD_1;
		foreach ($events as $event) {
			switch ($event->getType()) {
				case 'goalkeeper':
					/** @var GoalkeeperEvent $event */
					if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
						$stat->setTeamBBullets($stat->getTeamBBullets() + $event->getBullets());
					} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
						$stat->setTeamABullets($stat->getTeamABullets() + $event->getBullets());
					}
					break;
				case 'goal':
					/** @var GoalEvent $event */
					if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
						$stat->setTeamAGoals($stat->getTeamAGoals() + 1);
					} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
						$stat->setTeamBGoals($stat->getTeamBGoals() + 1);
					}
					$lastEventPeriod = $event->getPeriod();
					break;
				case 'penalty':
					/** @var PenaltyEvent $event */
					$time = $event->getPenaltyTime();
					if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
						$stat->setTeamAPenaltyTime($stat->getTeamAPenaltyTime() + $time);
					} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
						$stat->setTeamBPenaltyTime($stat->getTeamBPenaltyTime() + $time);
					}
					$lastEventPeriod = $event->getPeriod();
					break;
				default:
					break;
			}
		}
		$stat->setWinPeriod($lastEventPeriod);
		$this->games[$game->getId()] = $stat;
		$cached = $this->cache->getItem('stat.games');
		$cached->tag(['games']);
		$cached->set($this->games);
		$this->cache->save($cached);
		return $this->games[$game->getId()];
	}
}