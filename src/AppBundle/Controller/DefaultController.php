<?php

namespace AppBundle\Controller;

use Doctrine\ORM\Query\Expr\Orx;
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
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM BlogBundle:Post p ORDER BY p.postedAt DESC')->setMaxResults(3);
        $posts = $query->getResult();
        return $this->render('index.twig', [
            'posts' => $posts
        ]);
    }

	/**
	 * @Route("/game/{id}", name="game")
	 */
	public function gameAction($id, Request $request)
	{
		$game = $this->get('domain.repository.game')->findById($id);
		$events = $this->get('domain.repository.game.events')->findByGame($game);
		$scoreA = 0;
		$scoreB = 0;
		$scoreMap = [];
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
			'scoreA' => $scoreA,
			'scoreB' => $scoreB,
			'scoreMap' => $scoreMap,
			'isStarted' => $game->getDatetime()->getTimestamp() <= (new\DateTime())->getTimestamp()
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
	 * @Route("/calendar", name="calendar")
	 */
	public function calendarAction(Request $request)
	{
		$currentSeason = $this->get('domain.repository.season')->findById($this->get('settings.manager')->getCurrentSeasonId());
		$leagueId = $request->get('league_id');
		$teamId = $request->get('team_id');
		$month = $request->get('month');
		$year = $request->get('year', $currentSeason->getYear());

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
				->andWhere(new Orx(':league = sta.league', ':league = stb.league'))
				->setParameter('league', $leagueId);
		}

		if (!empty($teamId)) {
			$builder
				->andWhere(new Orx(':team = sta.team', ':team = stb.team'))
				->setParameter('team', $teamId);
		}

		if (!empty($month)) {
			$builder
				->andWhere('g.datetime >= :start')
				->andWhere('g.datetime < :end')
				->setParameter('start', sprintf('%d-%d-1 00:00:00', $year, $month))
				->setParameter(
					'end',
					sprintf('%d-%d-1 00:00:00', $month == 12 ? $year + 1 : $year, $month == 12 ? 1 : $month)
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

	/**
	 * @Route("/media", name="media")
	 */
	public function mediaAction(Request $request)
	{
		return $this->render('media.twig');
	}
}
