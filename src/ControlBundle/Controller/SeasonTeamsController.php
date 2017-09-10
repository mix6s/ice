<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 04.09.2017
 * Time: 19:56
 */

namespace ControlBundle\Controller;


use Domain\DTO\Request\AddSeasonTeamMemberRequest;
use Domain\DTO\Request\CreateSeasonTeamRequest;
use DomainBundle\Repository\SeasonTeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SeasonTeamsController
 * @package ControlBundle\Controller
 */
class SeasonTeamsController extends Controller
{

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
	 * @Route("/seasonteam/save", name="control.seasonteam.save")
	 */
	public function seasonTeamSaveAction(Request $request)
	{
		$seasonTeam = $request->request->get('seasonteam');

		$team = $this->get('app.team_manager')->saveTeam($seasonTeam['team']);
		$league = $this->get('app.team_manager')->saveLeague($seasonTeam['league']);
		$seasonId = $seasonTeam['season']['id'] ?? 0;
		$coachId = $seasonTeam['coach']['id'] ?? 0;

		if (empty($seasonTeam['id'])) {
			$response = $this
				->get('domain.use_case.create_season_team_use_case')
				->execute(new CreateSeasonTeamRequest($team->getId(), $coachId, $seasonId, $league->getId()));
			$st = $response->getSeasonTeam();
		} else {
			$coach = $this->get('domain.container')->getPlayerRepository()->findById($coachId);
			$st = $this->get('domain.container')->getSeasonTeamRepository()->findById($seasonTeam['id']);
			$st->changeCoach($coach);
			$st->changeLeague($league);
		}
		$this->get('domain.use_case.remove_season_team_members_use_case')->execute($st->getId());

		$members = $seasonTeam['members'] ?? [];

		$addRequest = new AddSeasonTeamMemberRequest($coachId, $st->getId());
		foreach ($members as $member) {
			$addRequest->addMember($member['player_id'], $member['type']);
		}
		$response = $this
			->get('domain.use_case.set_season_team_members_use_case')
			->execute($addRequest);

		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json(['seasonteam' => $st, 'members' => $response->getMembers()]);
	}
}