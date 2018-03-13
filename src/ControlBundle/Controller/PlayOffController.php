<?php

namespace ControlBundle\Controller;


use Domain\DTO\Request\SavePlayOffItemRequest;
use Domain\DTO\Request\SavePlayOffRequest;
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
			->leftJoin('pl.season', 's')
			->leftJoin('pl.league', 'l')
			->leftJoin('l.metadata', 'lm')
			->leftJoin('Domain:PlayOffItem', 'pli', 'WITH', 'pli.playOff = pl.id')
			->leftJoin('Domain:Game', 'g', 'WITH', 'g.playOffItem = pli.id')
			->orderBy('pl.id', 'desc')
			->addOrderBy('pli.rank','ASC')
			->addOrderBy('pli.id','ASC');

		return $this->json([
			'playoffs' => array_values(array_filter($qb->select('pl')->getQuery()->getResult(), function ($item) {
				return $item !== null;
			})),
			'playoffsItems' => array_values(array_filter($qb->select('pli')->getQuery()->getResult(), function ($item) {
				return $item !== null;
			})),
			'playoffsGames' => array_values(array_filter($qb->select('g')->getQuery()->getResult(), function ($item) {
				return $item !== null;
			})),
		]);
	}

	/**
	 * @Route("/playoff/save", name="control.playoff.save")
	 */
	public function playoffSaveAction(Request $request)
	{
		$season = $request->request->get('season');
		$league = $request->request->get('league');
		$startAt = new \DateTime($request->get('start_at'));
		$playoff = $this->get('domain.use_case.create_play_off_use_case')
			->execute(new SavePlayOffRequest($season['id'], $league['id'], $startAt, $request->request->get('id')))
			->getPlayOff();
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json(['playoff' => $playoff]);
	}

	/**
	 * @Route("/playoff/item/save", name="control.playoff.item.save")
	 */
	public function playoffItemSaveAction(Request $request)
	{
		$createRequest = new SavePlayOffItemRequest($request->request->get('playoff', ['id' => null])['id'], $request->request->get('rank'), $request->request->get('id'));
		$createRequest->setSeasonteamAId($request->request->get('seasonteamA', ['id' => null])['id'] ?? null);
		$createRequest->setSeasonteamBId($request->request->get('seasonteamB', ['id' => null])['id'] ?? null);
		$createRequest->setWinnerId($request->request->get('winner', ['id' => null])['id'] ?? null);
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

	/**
	 * @Route("/playoff/item/delete/{id}", name="control.playoff.item.delete")
	 */
	public function playoffItemDeleteAction($id)
	{
		$item = $this->get('domain.repository.playoffitem')->findById($id);
		$this->get('domain.repository.playoffitem')->remove($item);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json([]);
	}
}