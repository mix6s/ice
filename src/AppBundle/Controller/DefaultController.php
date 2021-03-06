<?php

namespace AppBundle\Controller;

use Domain\Entity\GoalEvent;
use Domain\Exception\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @throws \UnexpectedValueException
     * @throws \LogicException
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM BlogBundle:Post p ORDER BY p.postedAt DESC')->setMaxResults(3);
        $posts = $query->getResult();
        $query = $em->createQuery('SELECT a FROM MediaBundle:Album a WHERE a.isActive = true ORDER BY a.id DESC')->setMaxResults(8);
        $albums = $query->getResult();
        return $this->render('index.twig', [
            'posts' => $posts,
            'albums' => $albums
        ]);
    }

	/**
	 * @Route("/game/{id}", name="game")
	 */
	public function gameAction($id, Request $request)
	{
		$game = $this->get('domain.repository.game')->findById($id);
		$events = $this->get('domain.repository.game.events')->findByGame($game);
		$scoreMap = [];
		$scoreA = 0;
		$scoreB = 0;
		$eventsByPeriod = [];
		foreach ($events as $event) {
			$num = $event->getPeriod();
			if (empty($eventsByPeriod[$num])) {
				$eventsByPeriod[$num] = [];
			}
			$eventsByPeriod[$num][] = $event;
			if ($event instanceof GoalEvent) {
				if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
					$scoreA++;
				} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
					$scoreB++;
				}
				$scoreMap[$event->getId()] = $scoreA . ':' . $scoreB;
			}
		}
		return $this->render('game.twig', [
			'game' => $game,
			'events' => $events,
			'eventsByPeriod' => $eventsByPeriod,
			'scoreMap' => $scoreMap,
			'statistic' => $this->get('app.statistic.aggregator')->getGameStatistic($game)
		]);
	}

	/**
	 * @Route("/team/{id}", name="team.index")
	 */
	public function teamAction($id, Request $request)
	{
		$team = $this->get('domain.repository.team')->findById($id);

		try {
			$currentSeason = $this->get('domain.repository.season')->findById($this->get('settings.manager')->getCurrentSeasonId());
			$seasonTeam = $this->get('domain.repository.seasonteam')->findByTeamAndSeason($team, $currentSeason);
			$members = $this->get('domain.repository.seasonteammember')->findBySeasonTeam($seasonTeam);
		} catch (EntityNotFoundException $e) {
			$seasonTeam = null;
			$members = [];
		}

		return $this->render('team.twig', [
			'team' => $team,
			'seasonteam' => $seasonTeam,
			'members' => $members
		]);
	}

	/**
	 * @Route("/about", name="about")
	 */
	public function aboutAction(Request $request)
	{
		return $this->render('about.twig');
	}

	/**
	 * @Route("/standings", name="standings.current")
	 */
	public function currentSeasonStatistic(Request $request)
	{
		$season = $this->get('domain.repository.season')->findById($this->get('settings.manager')->getCurrentSeasonId());
		return $this->forward('AppBundle:Default:seasonStatistic', ['year' => $season->getBeggingYear()]);
	}

	/**
	 * @Route("/standings/{year}", name="standings.season")
	 */
	public function seasonStatisticAction(int $year, Request $request)
	{
		if ($year === null) {
			$season = $this->get('domain.repository.season')->findById($this->get('settings.manager')->getCurrentSeasonId());
		} else {
			$season = $this->get('domain.repository.season')->findByYear($year + 1);
		}
		return $this->render('tables.twig', ['season' => $season]);
	}
    /**
     * @Route("/top", name="top_stat")
     */
    public function topStatAction(Request $request)
    {
		$tops = [
			'forward' => 'Бомбардиры',
			'sniper' => 'Снайперы',
			'assistant' => 'Ассистенты',
			'back' => 'Защитники',
			'goalkeeper' => 'Вратари',
			'penalty' => 'Штрафники'
		];
		$top = $request->get('top', 'forward');
		if (!in_array($top, array_keys($tops))) {
			throw $this->createNotFoundException();
		}
		$leagues = $this->get('domain.repository.league')->findAll();
		$leagueId = (int)$request->get('league_id', $leagues[0]->getId());
		$league = $this->get('domain.repository.league')->findById($leagueId);
		$season = $this->get('domain.repository.season')->findById($this->get('settings.manager')->getCurrentSeasonId());
		$statistic = $this->get('app.statistic.aggregator')->getSeasonStatistic($season);
		$leagueBests = $statistic->getBestsByLeague($league);
		switch ($top) {
			case 'sniper':
				$bestList = $leagueBests->getBestSniperList();
				break;
			case 'goalkeeper':
				$bestList = $leagueBests->getBestGoalkeeperList();
				break;
			case 'assistant':
				$bestList = $leagueBests->getBestAssistantList();
				break;
			case 'back':
				$bestList = $leagueBests->getBestBackList();
				break;
			case 'forward':
				$bestList = $leagueBests->getBestForwardList();
				break;
			case 'penalty':
				$bestList = $leagueBests->getBestPenaltyList();
				break;
		}
        return $this->render('stat.twig', [
        	'list' => $bestList,
			'tops' => $tops,
			'leagues' => $leagues,
			'leagueId' => $leagueId,
			'top' => $top,
		]);
    }
	/**
	 * @Route("/calendar", name="calendar")
	 */
	public function calendarAction(Request $request)
	{
		$currentSeason = $this->get('domain.repository.season')->findById($this->get('settings.manager')->getCurrentSeasonId());
		$leagueId = $request->get('league_id');
		$teamId = $request->get('team_id');
		$month = (int)$request->get('month');
		$year = (int)$request->get('year', $currentSeason->getYear());

		$builder = $this->get('domain.repository.game')
			->createQueryBuilder('g')
			->select('g')
			->join('g.season', 's')
			->join('g.seasonTeamA', 'sta')
			->join('g.seasonTeamB', 'stb')
			->where('s.year = :season')
			->setParameter('season', $year)
		;
		if (!empty($leagueId)) {
			$builder
				->andWhere($builder->expr()->orX('sta.league = :league1', 'stb.league = :league2'))
				->setParameter('league1', $leagueId)
				->setParameter('league2', $leagueId);
		}

		if (!empty($teamId)) {
			$builder
				->andWhere($builder->expr()->orX('sta.team = :team1', 'stb.team = :team2'))
				->setParameter('team1', $teamId)
				->setParameter('team2', $teamId);
		}

		if (!empty($month)) {
			$builder
				->andWhere('g.datetime >= :start')
				->andWhere('g.datetime < :end')
				->setParameter(
					'start',
					new \DateTime(sprintf('%s-%s-01 00:00:00', $year - 1, $month)),
					\Doctrine\DBAL\Types\Type::DATETIME
				)
				->setParameter(
					'end',
					new \DateTime(sprintf('%s-%s-01 00:00:00', $month == 12 ? $year : $year - 1, $month == 12 ? 1 : $month + 1)),
					\Doctrine\DBAL\Types\Type::DATETIME
				);
		}
		$games = $builder
			->orderBy('g.datetime', 'DESC')
			->getQuery()
			->getResult()
		;

		return $this->render('calendar.twig', [
			'games' => $games,
			'leagues' => $this->get('domain.repository.league')->findAll(),
			'teams' => $this->get('domain.repository.team')->findAll(),
			'seasons' => $this->get('domain.repository.season')->findBy([], ['year' => 'DESC']),
			'filter' => [
				'league' => $leagueId,
				'month' => $month,
				'year' => $year,
				'team' => $teamId,
			]
		]);
	}
}
