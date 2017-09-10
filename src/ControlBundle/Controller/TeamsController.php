<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 04.09.2017
 * Time: 19:56
 */

namespace ControlBundle\Controller;


use DomainBundle\Entity\TeamMetadata;
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
			->leftJoin('Domain:SeasonTeamMember', 'stm', 'WITH', 'stm.seasonTeam = st.id')
			->orderBy('t.id, st.id', 'desc');

		return $this->json([
			'teams' => $qb->select('t')->getQuery()->getResult(),
			'seasonteams' => $qb->select('st')->getQuery()->getResult(),
			'seasonteam_members' => $qb->select('stm')->getQuery()->getResult(),
		]);
	}

	/**
	 * @Route("/teams/delete/{id}", name="control.teams.delete")
	 */
	public function deleteAction($id)
	{
		$this->get('domain.use_case.remove_team_use_case')->execute($id);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json([]);
	}

	/**
	 * @Route("/teams/save", name="control.teams.save")
	 */
	public function saveAction(Request $request)
	{
		$teamRequestData = $request->request->get('team', []);
		$team = $this->get('app.team_manager')->saveTeam($teamRequestData);
		return $this->json($team);
	}
}