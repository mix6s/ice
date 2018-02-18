<?php

namespace ControlBundle\Controller;


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
			->from('Domain:League', 'l')
			->leftJoin('Domain:SeasonTeam', 'st', 'WITH', 'st.league = l.id')
			->orderBy('l.id, st.id', 'desc');

		return $this->json([
			'playoffItems' => [],
		]);
	}
}