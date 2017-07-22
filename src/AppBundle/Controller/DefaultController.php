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
        return $this->render('index.twig');
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
