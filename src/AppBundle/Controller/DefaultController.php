<?php

namespace AppBundle\Controller;

use Domain\Entity\GoalEvent;
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
			$num = (int)($event->getSecondsFromStart() / (20 * 60));
			$num = $num > 4 ? 4 : $num;
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
		return $this->render('calendar.twig');
	}

	/**
	 * @Route("/media", name="media")
	 */
	public function mediaAction(Request $request)
	{
		return $this->render('media.twig');
	}
}
