<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.09.2017
 * Time: 20:00
 */

namespace ControlBundle\Controller;


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
		$game = $this->get('domain.use_case.save_game_use_case')->execute(
			$request->request->get('id'),
			$request->request->get('type'),
			$request->request->get('place'),
			$request->request->get('datetime'),
			$request->request->get('season', [])['id'],
			$request->request->get('seasonteamA', [])['id'],
			$request->request->get('seasonteamB', [])['id']
		);
		$this->get('doctrine.orm.entity_manager')->flush();

		return $this->json(['game' => $game]);
	}
}