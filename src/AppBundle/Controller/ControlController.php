<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 16:37
 */

namespace AppBundle\Controller;


use Domain\DTO\Request\CreateSeasonRequest;
use Domain\Entity\League;
use Domain\Exception\DomainException;
use Domain\Exception\SeasonAlreadyExistException;
use DomainBundle\Entity\LeagueMetadata;
use DomainBundle\Repository\LeagueRepository;
use DomainBundle\Repository\SeasonRepository;
use DomainBundle\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ControlController
 * @package AppBundle\Controller
 * @Route("/control")
 */
class ControlController extends Controller
{
	/**
	 * @Route("/test", name="control.test")
	 */
	public function testAction()
	{
		/** @var TeamRepository $teamRepository */
		$teamRepository = $this->get('domain.repository.team');

		/** @var LeagueRepository $leagueRepository */
		$leagueRepository = $this->get('domain.repository.league');
		$leagueMeta = new LeagueMetadata();
		$id = $leagueRepository->getNextId();
		$leagueMeta->setTitle('League ' . $id);
		$league = League::create($id, $leagueMeta);
		//$this->get('doctrine.orm.entity_manager')->persist($league);
		//$this->get('doctrine.orm.entity_manager')->flush();
	}

	/**
	 * @Route("", name="control.index")
	 */
	public function indexAction()
	{
		return $this->render('control/index.html.twig', [
		]);
	}

	/**
	 * @Route("/seasons", name="control.seasons")
	 */
	public function seasonsAction(Request $request)
	{
		if ($request->isXmlHttpRequest()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$qb
				->select('s')
				->from('Domain:Season', 's')
				->leftJoin('Domain:SeasonTeam', 'st', 'WITH', 'st.season = s.id')
				->leftJoin('Domain:Team', 't', 'WITH', 'st.team = t.id')
				->leftJoin('Domain:Player', 'c', 'WITH', 'st.coach = c.id')
				->leftJoin('Domain:League', 'l', 'WITH', 'st.league = l.id')
				->orderBy('s.year', 'desc');

			/** @var SeasonRepository $seasonRepository */
			$seasonRepository = $this->get('domain.repository.season');
			$seasons = $seasonRepository->findBy([], ['year' => 'desc']);
			return $this->json($qb->getQuery()->getResult());
		}
		return $this->render('control/seasons.html.twig');
	}

	/**
	 * @Route("/seasons/new", name="control.seasons.new")
	 */
	public function seasonsNewAction(Request $request)
	{
		$year = $request->request->get('year');
		try {
			$response = $this->get('domain.usecase.create_season_use_case')->execute(new CreateSeasonRequest($year));
		} catch (SeasonAlreadyExistException $exception) {
			return $this->json(['error' => 'Данный сезон уже существует'], 500);
		}
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json($response->getSeason());
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