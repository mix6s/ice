<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 19.09.2017
 * Time: 22:33
 */

namespace AppBundle;


use Domain\Entity\Game;
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
		return $builder
			->select('g')
			->from('Domain:Game', 'g')
			->orderBy('g.datetime', 'desc')
			->setMaxResults('10')
			->getQuery()
			->getResult();

	}

	public function __get($name)
	{
		$methodName = 'get' . ucfirst($name);
		return $this->$methodName();
	}
}