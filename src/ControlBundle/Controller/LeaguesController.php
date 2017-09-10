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
 * Class LeaguesController
 * @package ControlBundle\Controller
 */
class LeaguesController extends Controller
{
	/**
	 * @Route("/leagues", name="control.leagues.list")
	 */
	public function listAction(Request $request)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->render('@Control/leagues/list.html.twig');
		}

		$em = $this->get('doctrine.orm.entity_manager');
		$qb = $em->createQueryBuilder();
		$qb
			->from('Domain:League', 'l')
			->leftJoin('Domain:SeasonTeam', 'st', 'WITH', 'st.league = l.id')
			->orderBy('l.id, st.id', 'desc');

		return $this->json([
			'leagues' => $qb->select('l')->getQuery()->getResult(),
			'seasonteams' => $qb->select('st')->getQuery()->getResult(),
		]);
	}

	/**
	 * @Route("/leagues/delete/{id}", name="control.leagues.delete")
	 */
	public function deleteAction($id)
	{
		$this->get('domain.use_case.remove_league_use_case')->execute($id);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json([]);
	}

	/**
	 * @Route("/leagues/save", name="control.leagues.save")
	 */
	public function saveAction(Request $request)
	{
		$leagueRequestData = $request->request->get('league', []);
		$league = $this->get('app.team_manager')->saveLeague($leagueRequestData);
		return $this->json($league);
	}
}