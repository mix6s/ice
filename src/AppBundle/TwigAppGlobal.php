<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 19.09.2017
 * Time: 22:33
 */

namespace AppBundle;


use Domain\Entity\Game;
use Domain\Entity\GoalEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TwigAppGlobal
 * @package AppBundle
 * @property $calendarGames
 */
class TwigAppGlobal implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * @return array
	 */
	public function getCurrentSeasonTeams()
	{
		$currentSeasonId = $this->container->get('settings.manager')->getCurrentSeasonId();
		if (empty($currentSeasonId)) {
            return [
                'byLeague' => [],
                'stats' => []
            ];
        }
		$season = $this->container->get('domain.repository.season')->findById($currentSeasonId);
		$teams = $this->container->get('domain.repository.seasonteam')->findBySeason($season);
		$byLeague = [];
		$stats = [];
		foreach ($teams as $seasonTeam) {
			$stats[$seasonTeam->getId()] = [0,0,0];
			if (!array_key_exists($seasonTeam->getLeague()->getId(), $byLeague)) {
				$byLeague[$seasonTeam->getLeague()->getId()] = ['league' => $seasonTeam->getLeague(), 'seasonteams' => []];
			}
			$byLeague[$seasonTeam->getLeague()->getId()]['seasonteams'][] = $seasonTeam;
		}
		return [
			'byLeague' => $byLeague,
			'stats' => $stats
		];
	}

	/**
	 * @return Game[]
	 */
	public function getCalendarGames()
	{
		$builder = $this->container->get('doctrine.orm.entity_manager')->createQueryBuilder();
		$now = new \DateTime();

		$games = $builder
			->select('g')
			->from('Domain:Game', 'g')
			->where('g.datetime > :now')
			->setParameter('now', $now)
			->orderBy('g.datetime', 'ASC')
			->setMaxResults('10')
			->getQuery()
			->getResult();
		/** @var Game|null $firstFuture */
		$firstFuture = $games[0] ?? null;
		$past = $builder
			->where('g.datetime <= :now')
			->orderBy('g.datetime', 'DESC')
			->getQuery()
			->getResult();

		foreach ($past as $game) {
			array_unshift($games, $game);
		}

		if (empty($firstFuture)) {
			$slide = 1;
		} else {
			$firstFutureNum = 0;
			/** @var Game $game */
			foreach ($games as $key => $game) {
				if ($game->getId() === $firstFuture->getId()) {
					$firstFutureNum = $key;
					break;
				}
			}
			$slide = $firstFutureNum - 2;
		}

		return ['games' => $games, 'slide' => $slide];
	}

	public function __get($name)
	{
		$methodName = 'get' . ucfirst($name);
		return $this->$methodName();
	}
}