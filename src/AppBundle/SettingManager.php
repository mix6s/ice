<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 02.08.2017
 * Time: 18:58
 */

namespace AppBundle;


use AppBundle\Entity\Setting;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class SettingManager
 * @package AppBundle
 */
class SettingManager
{
	const CACHE_KEY = 'settings';
	const KEY_CURRENT_SEASON_ID = 'current_season_id';

	private $cache;
	private $em;
	private $settings;

	/**
	 * SettingsRepository constructor.
	 * @param EntityManager $em
	 * @param AdapterInterface $cache
	 */
	public function __construct(EntityManager $em, AdapterInterface $cache)
	{
		$this->em = $em;
		$this->cache = $cache;
	}

	/**
	 * @param string $name
	 * @param mixed|null $default
	 * @return mixed|null
	 */
	public function get(string $name, $default = null)
	{
		$setting = $this->getSetting($name);
		return $setting ? $setting->getValue() : $default;
	}

	/**
	 * @param string $name
	 * @param $value
	 */
	public function set(string $name, $value)
	{
		$setting = $this->getSetting($name);
		if ($setting === null) {
			$setting = new Setting($name);
		}
		$setting->setValue($value);
		$this->em->persist($setting);
		$this->em->flush($setting);
		$this->cache->deleteItem(self::CACHE_KEY);
	}

	/**
	 * @param string $name
	 * @return Setting|null
	 */
	private function getSetting(string $name)
	{
		$settings = $this->getSettings();
		foreach ($settings as $setting) {
			if ($setting->getName() === $name) {
				return $setting;
			}
		}
		return null;
	}

	/**
	 * @return Setting[]
	 */
	private function getSettings(): array
	{
		if (!empty($this->settings)) {
			return $this->settings;
		}
		$this->settings = $this->cache->getItem(self::CACHE_KEY)->get();
		if (!empty($this->settings)) {
			return $this->settings;
		}

		$qb = $this->em->createQueryBuilder();
		$this->settings = $qb->from('AppBundle:Setting', 's')->select('s')->getQuery()->getResult();
		$cached = $this->cache->getItem(self::CACHE_KEY);
		$cached->set($this->settings);
		$this->cache->save($cached);
		if (!empty($this->settings)) {
			return $this->settings;
		}
		return [];
	}

	/**
	 * @return int|null
	 */
	public function getCurrentSeasonId()
	{
		return $this->get(self::KEY_CURRENT_SEASON_ID);
	}

	/**
	 * @param int $value
	 */
	public function setCurrentSeasonId(int $value)
	{
		$this->set(self::KEY_CURRENT_SEASON_ID, $value);
	}
}