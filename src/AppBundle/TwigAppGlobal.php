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
	 * @return Game[]
	 */
	public function getCalendarGames()
	{
		$builder = $this->container->get('doctrine.orm.entity_manager')->createQueryBuilder();
		$now = new \DateTime();

		$games = $builder
			->select('g', 's', 'sta', 'stb', 'sta_t', 'stb_t', 'sta_tm', 'stb_tm')
			->from('Domain:Game', 'g')
			->join('g.season', 's')
			->join('g.seasonTeamA', 'sta')
			->join('g.seasonTeamB', 'stb')
			->join('sta.team', 'sta_t')
			->join('stb.team', 'stb_t')
			->join('stb_t.metadata', 'stb_tm')
			->join('sta_t.metadata', 'sta_tm')
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