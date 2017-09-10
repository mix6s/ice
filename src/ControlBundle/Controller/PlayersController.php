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
 * Class PlayersController
 * @package ControlBundle\Controller
 */
class PlayersController extends Controller
{
	/**
	 * @Route("/players", name="control.players.list")
	 */
	public function listAction(Request $request)
	{
		$page = $request->get('page', 1);
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$players = $this->get('domain.repository.player')->findPlayers($limit, $offset);
		$count = $this->get('domain.repository.player')->countPlayers();
		return $this->render(
			'@Control/players/list.html.twig',
			[
				'players' => $players,
				'currentPage' => $page,
				'pagesCount' => ceil($count / $limit)
			]
		);
	}
}