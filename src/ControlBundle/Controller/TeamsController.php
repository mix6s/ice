<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 04.09.2017
 * Time: 19:56
 */

namespace ControlBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeamsController
 * @package ControlBundle\Controller
 */
class TeamsController extends Controller
{
	/**
	 * @Route("/teams", name="control.teams.list")
	 */
	public function listAction(Request $request)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->render('@Control/teams/list.html.twig');
		}

		$em = $this->get('doctrine.orm.entity_manager');
		$qb = $em->createQueryBuilder();
		$qb
			->from('Domain:Team', 't')
			->leftJoin('Domain:SeasonTeam', 'st', 'WITH', 'st.team = t.id')
			->orderBy('t.id, st.id', 'desc');

		return $this->json([
			'teams' => $qb->select('t')->getQuery()->getResult(),
			'seasonteams' => $qb->select('st')->getQuery()->getResult(),
		]);
	}
}