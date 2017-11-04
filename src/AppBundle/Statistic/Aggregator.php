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
		if (array_key_exists($season->getId(), $this->top)) {
			return $this->top[$season->getId()];
		}

		$this->top[$season->getId()] = $this->cache->getItem('stat.season.top.' . $season->getId())->get();
		if (!empty($this->top[$season->getId()])) {
			return $this->top[$season->getId()];
		}

		$members = $this->seasonTeamMemberRepository->findBySeason($season);
		/** @var \AppBundle\Statistic\SeasonTeamMember $bestAssistant */
		$bestAssistant = null;
		/** @var \AppBundle\Statistic\SeasonTeamMember $bestForward */
		$bestForward = null;
		/** @var \AppBundle\Statistic\SeasonTeamMember $bestSniper */
		$bestSniper = null;
		/** @var \AppBundle\Statistic\SeasonTeamMember $bestBack */
		$bestBack = null;
		/** @var \AppBundle\Statistic\SeasonTeamMember $bestGoalkeeper */
		$bestGoalkeeper = null;
		foreach ($members as $member) {
			$stat = $this->getSeasonTeamMemberStatistic($member);
			/** @var PlayerMetadata $playerMeta */
			$playerMeta = $member->getPlayer()->getMetadata();
			$stat->getMember()->getPlayer()->setMetadata($playerMeta);
			if ($bestAssistant === null) {
				$bestAssistant = $stat;
			} elseif ($bestAssistant->getAssistantGoals() < $stat->getAssistantGoals()) {
				$bestAssistant = $stat;
			} elseif ($bestAssistant->getAssistantGoals() === $stat->getAssistantGoals()
				&& $bestAssistant->getGamesCount() > $stat->getGamesCount()
			) {
				$bestAssistant = $stat;
			}


			if ($bestForward === null) {
				$bestForward = $stat;
			} elseif ($bestForward->getForwardScore() < $stat->getForwardScore()) {
				$bestForward = $stat;
			} elseif ($bestForward->getForwardScore() === $stat->getForwardScore()
				&& $bestForward->getGoals() < $stat->getGoals()
			) {
				$bestForward = $stat;
			} elseif ($bestForward->getForwardScore() === $stat->getForwardScore()
				&& $bestForward->getGoals() === $stat->getGoals()
				&& $bestForward->getGamesCount() > $stat->getGamesCount()
			) {
				$bestForward = $stat;
			}

			if ($bestSniper === null) {
				$bestSniper = $stat;
			} elseif ($bestSniper->getGoals() < $stat->getGoals()) {
				$bestSniper = $stat;
			} elseif ($bestSniper->getGoals() === $stat->getGoals()
				&& $bestSniper->getGamesCount() > $stat->getGamesCount()
			) {
				$bestSniper = $stat;
			}

			if ($playerMeta->isPositionBack()) {
				if ($bestBack === null) {
					$bestBack = $stat;
				} elseif ($bestBack->getForwardScore() < $stat->getForwardScore()) {
					$bestBack = $stat;
				} elseif ($bestBack->getForwardScore() === $stat->getForwardScore()
					&& $bestBack->getGoals() < $stat->getGoals()
				) {
					$bestBack = $stat;
				} elseif ($bestBack->getForwardScore() === $stat->getForwardScore()
					&& $bestBack->getGoals() === $stat->getGoals()
					&& $bestBack->getGamesCount() > $stat->getGamesCount()
				) {
					$bestBack = $stat;
				}
			}

			if ($playerMeta->isPositionGoalkeeper() && $stat->getTotalSecondsTime() > 0) {
				if ($bestGoalkeeper === null) {
					$bestGoalkeeper = $stat;
				} elseif ($bestBack->getReliabilityCoef() > $stat->getReliabilityCoef()) {
					$bestBack = $stat;
				} elseif ($bestBack->getReliabilityCoef() === $stat->getReliabilityCoef()
					&& $bestBack->getTotalSecondsTime() < $stat->getTotalSecondsTime()
				) {
					$bestBack = $stat;
				}
			}
		}

		$this->top[$season->getId()] = [
			'sniper' => $bestSniper,
			'assistant' => $bestAssistant,
			'goalkeeper' => $bestGoalkeeper,
			'back' => $bestBack,
			'forward' => $bestForward,
		];
		$cached = $this->cache->getItem('stat.season.top.' . $season->getId());
		$cached->tag(['season.' . $season->getId()]);
		$cached->set($this->top[$season->getId()]);
		$this->cache->save($cached);
		return $this->top[$season->getId()];
	}

	/**
	 * @param Season $season
	 * @return \AppBundle\Statistic\SeasonTeam[]
	 */
	public function getSeasonStatistic(Season $season)
	{
		if (array_key_exists($season->getId(), $this->seasons)) {
			return $this->seasons[$season->getId()];
		}

		$this->seasons[$season->getId()] = $this->cache->getItem('stat.season.' . $season->getId())->get();
		if (!empty($this->seasons[$season->getId()])) {
			return $this->seasons[$season->getId()];
		}

		$stat = [];
		$teams = $this->seasonTeamRepository->findBySeason($season);
		foreach ($teams as $seasonTeam) {
			$stat[] = $this->getSeasonTeamStatistic($seasonTeam);
		}
		usort($stat, [$this, 'sortSeasonTeams']);
		$this->seasons[$season->getId()] = $stat;
		$cached = $this->cache->getItem('stat.season.' . $season->getId());
		$cached->tag(['season.' . $season->getId()]);
		$cached->set($stat);
		$this->cache->save($cached);
		return $this->seasons[$season->getId()];
	}

	/**
	 * @param \AppBundle\Statistic\SeasonTeam $teamA
	 * @param \AppBundle\Statistic\SeasonTeam $teamB
	 * @return int
	 */
	private function sortSeasonTeams(\AppBundle\Statistic\SeasonTeam $teamA, \AppBundle\Statistic\SeasonTeam $teamB)
	{
		//набравшая наибольшее количество очков во всех матчах;
		if ($teamA->getScores() < $teamB->getScores()) {
			return 1;
		} elseif ($teamA->getScores() > $teamB->getScores()) {
			return -1;
		}

		//набравшая наибольшее количество очков во всех матчах между собой;
		if ($teamA->getScores($teamB->getSeasonTeam()) < $teamB->getScores($teamA->getSeasonTeam())) {
			return 1;
		} elseif ($teamA->getScores($teamB->getSeasonTeam()) > $teamB->getScores($teamA->getSeasonTeam())) {
			return -1;
		}

		//имеющая лучшую разницу забитых и пропущенных шайб во всех играх между этими командами;
		if ($teamA->getGoals($teamB->getSeasonTeam()) < $teamB->getGoals($teamA->getSeasonTeam())) {
			return 1;
		} elseif ($teamA->getGoals($teamB->getSeasonTeam()) > $teamB->getGoals($teamA->getSeasonTeam())) {
			return -1;
		}

		//имеющая лучшую разницу забитых и пропущенных шайб во всех матчах;
		if ($teamA->getGoals() - $teamA->getGoalsFailed() < $teamB->getGoals() - $teamB->getGoalsFailed()) {
			return 1;
		} elseif ($teamA->getGoals() - $teamA->getGoalsFailed() > $teamB->getGoals() - $teamB->getGoalsFailed()) {
			return -1;
		}

		//имеющая лучшее соотношение забитых и пропущенных шайб во всех матчах;
		if ($teamA->getGoals() / $teamA->getGoalsFailed() < $teamB->getGoals() / $teamB->getGoalsFailed()) {
			return 1;
		} elseif ($teamA->getGoals() / $teamA->getGoalsFailed() > $teamB->getGoals() / $teamB->getGoalsFailed()) {
			return -1;
		}

		//имеющая наибольшее число побед во всех матчах;
		if ($teamA->getWinCount() < $teamB->getWinCount()) {
			return 1;
		} elseif ($teamA->getWinCount() > $teamB->getWinCount()) {
			return -1;
		}

		//забросившая наибольшее количество шайб во всех матчах этапа.
		if ($teamA->getGoals() < $teamB->getGoals()) {
			return 1;
		} elseif ($teamA->getGoals() > $teamB->getGoals()) {
			return -1;
		}
		return 0;
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
		if (array_key_exists($member->getId(), $this->seasonTeamMembers)) {
			return $this->seasonTeamMembers[$member->getId()];
		}

		$this->seasonTeamMembers[$member->getId()] = $this->cache->getItem('stat.member.' . $member->getId())->get();
		if (!empty($this->seasonTeamMembers[$member->getId()])) {
			return $this->seasonTeamMembers[$member->getId()];
		}

		$stat = new \AppBundle\Statistic\SeasonTeamMember($member);
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
			$memberIsGoalkeeper = false;
			$memberGameGoalsFailed = 0;
			foreach ($events as $event) {
				switch ($event->getType()) {
					case 'goalkeeper':
						/** @var GoalkeeperEvent $event */
						if ($event->getMember()->getId() === $member->getId()) {
							$memberIsGoalkeeper = true;
							$memberGameGoalsFailed = $event->getGoals();
							$stat->setGoalsFailed($stat->getGoalsFailed() + $event->getGoals());
							$stat->setTotalSecondsTime($stat->getTotalSecondsTime() + $event->getDuration());
						}
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
			if ($memberIsGoalkeeper) {
				$stat->setGamesCountAsGoalkeeper($stat->getGamesCountAsGoalkeeper() + 1);
				if ($memberGameGoalsFailed === 0) {
					$stat->setZeroGameCount($stat->getZeroGameCount() + 1);
				}
			}
		}
		$this->seasonTeamMembers[$member->getId()] = $stat;
		$cached = $this->cache->getItem('stat.member.' . $member->getId());
		$cached->tag(['member.' . $member->getId(), 'season.' . $member->getSeasonTeam()->getSeason()->getId()]);
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
					$time = $this->getPenaltyTime($event->getPenaltyTimeType());
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
		$cached->tag(['game.' . $game->getId()]);
		$cached->set($this->games);
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