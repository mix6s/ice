<?php

namespace AppBundle\Controller;

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
		return $this->render('game.twig', [
			'game' => $this->get('domain.repository.game')->findById($id)
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
	 * @Route("/news", name="news")
	 */
	public function newsAction(Request $request)
	{
		return $this->render('news.twig');
	}

	/**
	 * @Route("/media", name="media")
	 */
	public function mediaAction(Request $request)
	{
		return $this->render('media.twig');
	}
}
