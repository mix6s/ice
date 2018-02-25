<?php

namespace ControlBundle\Controller;


use Domain\DTO\Request\CreatePlayOffGridItemRequest;
use Domain\DTO\Request\CreatePlayOffRequest;
use Domain\DTO\Request\RemovePlayOffRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlayOffController extends Controller
{
	/**
	 * @Route("/playoff", name="control.playoff.list")
	 */
	public function listAction(Request $request)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->render('@Control/playoff/list.html.twig');
		}

		$em = $this->get('doctrine.orm.entity_manager');
		$qb = $em->createQueryBuilder();
		$qb
			->from('Domain:PlayOff', 'pl')
			//->leftJoin('Domain:SeasonTeam', 'st', 'WITH', 'st.league = l.id')
			->orderBy('pl.id', 'desc');

		return $this->json([
			'playoffItems' => $qb->select('pl')->getQuery()->getResult(),
		]);
	}

	/**
	 * @Route("/playoff/new", name="control.playoff.new")
	 */
	public function playoffNewAction(Request $request)
	{
		$season = $request->request->get('season');
		$league = $request->request->get('league');
		$startAt = new \DateTime($request->get('start_at'));
		$playoff = $this->get('domain.use_case.create_play_off_use_case')
			->execute(new CreatePlayOffRequest($season['id'], $league['id'], $startAt))
			->getPlayOff();
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json(['playoff' => $playoff]);
	}

	/**
	 * @Route("/playoff/item/new", name="control.playoff.item.new")
	 */
	public function playoffItemNewAction(Request $request)
	{
		$createRequest = new CreatePlayOffGridItemRequest($request->request->get('playoff', ['id' => null])['id'], $request->request->get('rank'));
		$createRequest->setSeasonteamAId($request->request->get('seasonteamA', ['id' => null])['id']);
		$createRequest->setSeasonteamBId($request->request->get('seasonteamB', ['id' => null])['id']);
		$createRequest->setWinnerId($request->request->get('winner', ['id' => null])['id']);
		$item = $this->get('domain.use_case.create_play_off_grid_item_use_case')
			->execute($createRequest)
			->getItem();
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json(['item' => $item]);
	}

	/**
	 * @Route("/playoff/delete/{id}", name="control.playoff.delete")
	 */
	public function playoffDeleteAction($id)
	{
		$this->get('domain.use_case.remove_play_off_use_case')->execute(new RemovePlayOffRequest($id));
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json([]);
	}
}