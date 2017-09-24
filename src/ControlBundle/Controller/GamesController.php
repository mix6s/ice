<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.09.2017
 * Time: 20:00
 */

namespace ControlBundle\Controller;


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
			$gameRequestData['seasonteamB']['id']
		);

		$eventsChangeRequest = new SaveGameEventsRequest($game->getId());
		foreach ($eventsRequestData as $data) {
			if ($data['type'] === 'goal') {
				$eventsChangeRequest->addGoalEventData(
					$data['_timeFormatted'],
					$data['member']['id'],
					!empty($data['assistant_a']) ? $data['assistant_a']['id'] : null,
					!empty($data['assistant_b']) ? $data['assistant_b']['id'] : null
				);
			} elseif ($data['type'] === 'penalty') {
				$eventsChangeRequest->addPenaltyEventData($data['_timeFormatted'], $data['member']['id'], $data['penalty_time_type']);
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
		$game = $this->get('domain.repository.game')->findById($id);
		$this->get('domain.repository.game')->remove($game);
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