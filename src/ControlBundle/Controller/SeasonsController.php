<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 04.09.2017
 * Time: 19:56
 */

namespace ControlBundle\Controller;


use Domain\DTO\Request\CopySeasonRequest;
use Domain\DTO\Request\CreateSeasonRequest;
use Domain\Exception\SeasonAlreadyExistException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SeasonsController
 * @package ControlBundle\Controller
 */
class SeasonsController extends Controller
{
	/**
	 * @Route("/seasons/current", name="control.seasons.current")
	 */
	public function seasonsCurrentAction(Request $request)
	{
		if ($request->getMethod() === 'POST') {
			$this->get('settings.manager')->setCurrentSeasonId($request->request->getInt('id'));
			return $this->json([]);
		}
	}

	/**
	 * @Route("/seasons", name="control.seasons.list")
	 */
	public function listAction(Request $request)
	{
		if ($request->isXmlHttpRequest()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$qb
				->from('Domain:Season', 's')
				->leftJoin('Domain:SeasonTeam', 'st', 'WITH', 'st.season = s.id')
				->leftJoin('Domain:Team', 't', 'WITH', 'st.team = t.id')
				->leftJoin('Domain:Player', 'c', 'WITH', 'st.coach = c.id')
				->leftJoin('Domain:League', 'l', 'WITH', 'st.league = l.id')
				->orderBy('s.year, st.id', 'desc');

			return $this->json([
				'seasons' => $qb->select('s')->getQuery()->getResult(),
				'seasonteams' => $qb->select('st')->getQuery()->getResult(),
				'currentSeasonId' => $this->get('settings.manager')->getCurrentSeasonId()
			]);
		}
		return $this->render('@Control/seasons/list.html.twig');
	}

	/**
	 * @Route("/seasons/new", name="control.seasons.new")
	 */
	public function seasonsNewAction(Request $request)
	{
		$year = $request->request->get('year');
		$copyId = (int)$request->request->get('copy_season_id');
		$seasonteams = [];
		try {
			if ($copyId) {
				$response = $this->get('domain.use_case.copy_season_use_case')->execute(new CopySeasonRequest($copyId, $year));
				$seasonteams = $response->getSeasonTeams();
			} else {
				$response = $this->get('domain.use_case.create_season_use_case')->execute(new CreateSeasonRequest($year));
			}
		} catch (SeasonAlreadyExistException $exception) {
			return $this->json(['error' => 'Данный сезон уже существует'], 500);
		}
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json(['season' => $response->getSeason(), 'seasonteams' => $seasonteams]);
	}

	/**
	 * @Route("/season/delete/{id}", name="control.season.delete")
	 */
	public function seasonDeleteAction($id)
	{
		$this->get('domain.use_case.remove_season_use_case')->execute($id);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json([]);
	}
}