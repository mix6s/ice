<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 16:37
 */

namespace AppBundle\Controller;


use Domain\DTO\Request\CreateSeasonRequest;
use Domain\DTO\Request\CreateSeasonTeamRequest;
use Domain\DTO\Request\CreateTeamRequest;
use Domain\Entity\League;
use Domain\Entity\Player;
use Domain\Entity\Team;
use Domain\Exception\DomainException;
use Domain\Exception\SeasonAlreadyExistException;
use DomainBundle\Entity\LeagueMetadata;
use DomainBundle\Entity\PlayerMetadata;
use DomainBundle\Entity\TeamMetadata;
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
		$this->get('settings.manager')->set('currentSeason', 4);
		$season = $this->get('settings.manager')->get('currentSeason');

		return $this->json([
			$season
		]);
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
		$this->get('app.cache');
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
	 * @Route("/typeahead", name="control.typeahead")
	 */
	public function typeaheadAction(Request $request)
	{
		$teamQuery = $request->get('team');
		if (!empty($teamQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$teams = $qb
				->from('Domain:Team', 't')
				->join('t.metadata', 'm')
				->select('t')
				->where('m.title like :query ')
				->setParameter('query', '%' . $teamQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var Team $team */
			foreach ($teams as $team) {
				/** @var TeamMetadata $meta */
				$meta = $team->getMetadata();

				$result[] = [
					'name' => $meta->getTitle(),
					'team' => $team
				];
			}
			return $this->json($result);
		}

		$coachQuery = $request->get('coach');
		if (!empty($coachQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$players = $qb
				->from('Domain:Player', 'p')
				->join('p.metadata', 'm')
				->select('p')
				->where('m.surname like :query')
				->orWhere('m.firstName like :query')
				->orWhere('m.secondName like :query')
				->setParameter('query', '%' . $coachQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var Player $player */
			foreach ($players as $player) {
				/** @var PlayerMetadata $meta */
				$meta = $player->getMetadata();

				$result[] = [
					'name' => $meta->getSurname(),
					'coach' => $player
				];
			}
			return $this->json($result);
		}
	}

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
	 * @Route("/seasons", name="control.seasons")
	 */
	public function seasonsAction(Request $request)
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
				->orderBy('s.year', 'desc');

			/** @var SeasonRepository $seasonRepository */
			$seasonRepository = $this->get('domain.repository.season');
			$seasons = $seasonRepository->findBy([], ['year' => 'desc']);

			return $this->json([
				'seasons' => $qb->select('s')->getQuery()->getResult(),
				'seasonteams' => $qb->select('st')->getQuery()->getResult(),
				'currentSeasonId' => $this->get('settings.manager')->getCurrentSeasonId()
			]);
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
			$response = $this->get('domain.use_case.create_season_use_case')->execute(new CreateSeasonRequest($year));
		} catch (SeasonAlreadyExistException $exception) {
			return $this->json(['error' => 'Данный сезон уже существует'], 500);
		}
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json($response->getSeason());
	}

	/**
	 * @Route("/team/new", name="control.team.new")
	 */
	public function teamNewAction(Request $request)
	{
		$team = $request->request->get('team');
		$meta = $team['metadata'];
		$metadata = new TeamMetadata();
		$metadata->setTitle($meta['title']);
		$response = $this->get('domain.use_case.create_team_use_case')->execute(new CreateTeamRequest($metadata));
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json($response->getTeam());
	}

	/**
	 * @Route("/seasonteam/save", name="control.seasonteam.save")
	 */
	public function seasonTeamSaveAction(Request $request)
	{
		$seasonTeam = $request->request->get('seasonteam');

		$team = $this->saveTeam($seasonTeam['team']);
		$seasonId = $seasonTeam['season']['id'] ?? 0;
		$coachId = $seasonTeam['coach']['id'] ?? 0;
		if (empty($seasonTeam['id'])) {
			$response = $this
				->get('domain.use_case.create_season_team_use_case')
				->execute(new CreateSeasonTeamRequest($team->getId(), $coachId, $seasonId, 1));
			$this->get('doctrine.orm.entity_manager')->flush();
			return $this->json($response->getSeasonTeam());
		}

		$this->get('doctrine.orm.entity_manager')->flush();
	}

	/**
	 * @param array $teamRequestData
	 * @return Team
	 */
	private function saveTeam(array $teamRequestData): Team
	{
		$id = $teamRequestData['id'] ?? null;
		$meta = $teamRequestData['metadata'];
		if (empty($id)) {
			$team = $this->get('domain.use_case.create_team_use_case')->execute(new CreateTeamRequest(new TeamMetadata()))->getTeam();
		} else {
			/** @var TeamRepository $repository */
			$repository = $this->get('domain.repository.team');
			$team = $repository->findById($id);
		}
		/** @var TeamMetadata $metadata */
		$metadata = $team->getMetadata();
		$metadata->setTitle($meta['title']);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $team;
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