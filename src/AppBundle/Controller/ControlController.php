<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 16:37
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ControlController
 * @package AppBundle\Controller
 * @Route("/control")
 */
class ControlController extends Controller
{
	/**
	 * @Route("", name="control.index")
	 */
	public function indexAction()
	{
		return $this->render('control/index.html.twig');
	}

	/**
	 * @Route("/seasons", name="control.seasons")
	 */
	public function seasonsAction()
	{
		return $this->render('control/seasons.html.twig');
	}

	/**
	 * @Route("/leagues", name="control.leagues")
	 */
	public function leaguesAction()
	{

	}

	/**
	 * @Route("/teams", name="control.teams")
	 */
	public function teamsAction()
	{

	}
}