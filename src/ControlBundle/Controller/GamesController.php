<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.09.2017
 * Time: 20:00
 */

namespace ControlBundle\Controller;


use Domain\DTO\GoalEventData;
use Domain\DTO\GoalkeeperData;
use Domain\DTO\PenaltyEventData;
use Domain\DTO\Request\SaveGameEventsRequest;
use Domain\Entity\GameEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GamesController
 * @package ControlBundle\Controller
 *
 * @Route("/games")
 */
class GamesController extends Controller
{
	/**
	 * @Route("", name="control.games.list")
	 */
	public function listAction(Request $request)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->render('@Control/games/list.html.twig');
		}
		$games = $this->get('domain.repository.game')->findAll();
		return $this->json([
			'games' => $games,
		]);

	}


	private function timeToSeconds(string $time)
	{
		$seconds = 0;
		$parts = explode(':', $time);
		if (isset($parts[0])) {
			$seconds += isset($parts[1]) ? $parts[0] * 60 : $parts[0];
		}

		if (isset($parts[1])) {
			$seconds += $parts[1];
		}
		return $seconds;
	}

	/**
	 * @Route("/save", name="control.games.save")
	 */
	public function saveAction(Request $request)
	{
		$gameRequestData = $request->request->get('game', []);
		$eventsRequestData = $request->request->get('events', []);
		$game = $this->get('domain.use_case.save_game_use_case')->execute(
			$gameRequestData['id'],
			$gameRequestData['type'],
			$gameRequestData['place'],
			$gameRequestData['datetime'],
			$gameRequestData['season']['id'],
			$gameRequestData['seasonteamA']['id'],
			$gameRequestData['seasonteamB']['id'],
			$gameRequestData['state'] == 'false' || empty($gameRequestData['state']) ? 0 : 1,
			$gameRequestData['membersA'] ?? [],
			$gameRequestData['membersB'] ?? []
		);

		$eventsChangeRequest = new SaveGameEventsRequest($game->getId());
		foreach ($eventsRequestData as $data) {
			if ($data['type'] === 'goal') {
				$eventsChangeRequest->addGoalEventData(
					new GoalEventData(
						(int)$this->timeToSeconds($data['_timeFormatted']),
						(int)$data['period'],
						(int)$data['member']['id'],
						!empty($data['assistant_a']) ? (int)$data['assistant_a']['id'] : null,
						!empty($data['assistant_b']) ? (int)$data['assistant_b']['id'] : null
					)
				);
			} elseif ($data['type'] === 'goalkeeper') {
				$eventsChangeRequest->addGoalkeeperData(
					new GoalkeeperData(
						(int)$data['goals'],
						(int)$data['bullets'],
						$data['time'],
						(int)$data['member']['id']
					)
				);
			} elseif ($data['type'] === 'penalty') {
				$eventsChangeRequest->addPenaltyEventData(
					new PenaltyEventData(
						(int)$this->timeToSeconds($data['_timeFormatted']),
						(int)$data['period'],
						(int)$data['member']['id'],
						$data['penalty_time_type']
					)
				);
			}
		}
		$events = $this->get('domain.use_case.save_game_events_use_case')->execute($eventsChangeRequest);
		$this->get('doctrine.orm.entity_manager')->flush();

		return $this->json(['game' => $game, 'events' => $events]);
	}

	/**
	 * @Route("/delete/{id}", name="control.games.delete")
	 */
	public function deleteAction($id)
	{
		$this->get('domain.use_case.remove_game_use_case')->execute($id);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json([]);
	}

	/**
	 * @Route("/events/{id}", name="control.games.events")
	 */
	public function eventsAction($id)
	{
		$game = $this->get('domain.repository.game')->findById($id);
		return $this->json($this->get('domain.repository.game.events')->findByGame($game));
	}
}