<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 16:37
 */

namespace AppBundle\Controller;


use Domain\DTO\Request\AddSeasonTeamMemberRequest;
use Domain\DTO\Request\CopySeasonRequest;
use Domain\DTO\Request\CreateLeagueRequest;
use Domain\DTO\Request\CreateSeasonRequest;
use Domain\DTO\Request\CreateSeasonTeamRequest;
use Domain\DTO\Request\CreateTeamRequest;
use Domain\Entity\League;
use Domain\Entity\Player;
use Domain\Entity\Season;
use Domain\Entity\Team;
use Domain\Exception\DomainException;
use Domain\Exception\SeasonAlreadyExistException;
use DomainBundle\Entity\LeagueMetadata;
use DomainBundle\Entity\PlayerMetadata;
use DomainBundle\Entity\TeamMetadata;
use DomainBundle\Repository\LeagueRepository;
use DomainBundle\Repository\SeasonRepository;
use DomainBundle\Repository\SeasonTeamRepository;
use DomainBundle\Repository\TeamRepository;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
					'name' => $meta->getFullName(),
					'coach' => $player
				];
			}
			return $this->json($result);
		}

		$playerQuery = $request->get('player');
		if (!empty($playerQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$players = $qb
				->from('Domain:Player', 'p')
				->join('p.metadata', 'm')
				->select('p')
				->where('m.surname like :query')
				->orWhere('m.firstName like :query')
				->orWhere('m.secondName like :query')
				->setParameter('query', '%' . $playerQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var Player $player */
			foreach ($players as $player) {
				/** @var PlayerMetadata $meta */
				$meta = $player->getMetadata();

				$result[] = [
					'name' => $meta->getFullName(),
					'player' => $player
				];
			}
			return $this->json($result);
		}

		$leagueQuery = $request->get('league');
		if (!empty($leagueQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$leagues = $qb
				->from('Domain:League', 'l')
				->join('l.metadata', 'm')
				->select('l')
				->where('m.title like :query')
				->setParameter('query', '%' . $leagueQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var League $league */
			foreach ($leagues as $league) {
				/** @var LeagueMetadata $meta */
				$meta = $league->getMetadata();

				$result[] = [
					'name' => $meta->getTitle(),
					'league' => $league
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
				->orderBy('s.year, st.id', 'desc');

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
	 * @Route("/seasonteam/members/{id}", name="control.seasonteam.members.get")
	 */
	public function seasonTeamMembersGetAction($id, Request $request)
	{
		/** @var SeasonTeamRepository $seasonTeamRepository */
		$seasonTeamRepository = $this->get('domain.repository.seasonteam');
		$seasonTeam = $seasonTeamRepository->findById($id);
		return $this->json($this->get('domain.repository.seasonteammember')->findBySeasonTeam($seasonTeam));
	}

	/**
	 * @Route("/seasonteam/delete/{id}", name="control.seasonteam.delete")
	 */
	public function seasonTeamDeleteAction($id)
	{
		$this->get('domain.use_case.remove_season_team_use_case')->execute($id);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json([]);
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

	/**
	 * @Route("/seasonteam/save", name="control.seasonteam.save")
	 */
	public function seasonTeamSaveAction(Request $request)
	{
		$seasonTeam = $request->request->get('seasonteam');

		$team = $this->saveTeam($seasonTeam['team']);
		$league = $this->saveLeague($seasonTeam['league']);
		$seasonId = $seasonTeam['season']['id'] ?? 0;
		$coachId = $seasonTeam['coach']['id'] ?? 0;

		if (empty($seasonTeam['id'])) {
			$response = $this
				->get('domain.use_case.create_season_team_use_case')
				->execute(new CreateSeasonTeamRequest($team->getId(), $coachId, $seasonId, $league->getId()));
			$this->get('doctrine.orm.entity_manager')->flush();
			$st = $response->getSeasonTeam();
		} else {
			$coach = $this->get('domain.container')->getPlayerRepository()->findById($coachId);
			$st = $this->get('domain.container')->getSeasonTeamRepository()->findById($seasonTeam['id']);
			$st->changeCoach($coach);
			$st->changeLeague($league);
		}
		$this->get('doctrine.orm.entity_manager')->flush();
		$this->get('domain.use_case.remove_season_team_members_use_case')->execute($st->getId());
		$this->get('doctrine.orm.entity_manager')->flush();

		$members = $seasonTeam['members'] ?? [];

		$addRequest = new AddSeasonTeamMemberRequest($coachId, $st->getId());
		foreach ($members as $member) {
			$addRequest->addMember($member['player_id'], $member['type']);
		}
		$response = $this
			->get('domain.use_case.add_season_team_members_use_case')
			->execute($addRequest);
		$this->get('doctrine.orm.entity_manager')->flush();

		return $this->json($st);
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
		$metadata->updateFromData($meta);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $team;
	}

	/**
	 * @param array $leagueRequestData
	 * @return League
	 */
	private function saveLeague(array $leagueRequestData): League
	{
		$id = $leagueRequestData['id'] ?? null;
		$meta = $leagueRequestData['metadata'];
		if (empty($id)) {
			$metadata = new LeagueMetadata();
			$metadata->setTitle($meta['title']);
			$league = $this->get('domain.use_case.create_league_use_case')->execute(new CreateLeagueRequest($metadata))->getLeague();
		} else {
			/** @var LeagueRepository $repository */
			$repository = $this->get('domain.repository.league');
			$league = $repository->findById($id);
		}
		/** @var LeagueMetadata $metadata */
		$metadata = $league->getMetadata();
		$metadata->setTitle($meta['title']);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $league;
	}

	/**
	 * @Route("/avatar/upload", name="control.team.avatar.upload")
	 */
	public function uploadTeamAvatarAction(Request $request)
	{
		/** @var UploadedFile $file */
		$file = $request->files->get('file');
		$tmpfilename = uniqid();
		$file->move($this->getParameter('web_dir') . '/backend/img/upload/', $tmpfilename);
		$binary = new Binary(file_get_contents($this->getParameter('web_dir') . '/backend/img/upload/' . $tmpfilename), 'image/png', 'png');
		$filename = uniqid('t_avatar_', true) . '.png';

		$response = $this->get('liip_imagine.filter.manager')->applyFilter($binary, 'avatar_mini');
		$f = fopen($this->getParameter('web_dir') . '/avatar/mini/' . $filename, 'w');
		fwrite($f, $response->getContent());
		fclose($f);

		$response = $this->get('liip_imagine.filter.manager')->applyFilter($binary, 'avatar_normal');
		$f = fopen($this->getParameter('web_dir') . '/avatar/' . $filename, 'w');
		fwrite($f, $response->getContent());
		fclose($f);
		return $this->json($filename);
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