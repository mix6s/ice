<?php

namespace AppBundle;

use AppBundle\Entity\PlayerProfile;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class TwigAppExtension
 * @package AppBundle
 */
class TwigAppExtension extends \Twig_Extension
{
	/**
	 * @return array
	 */
	public function getFilters()
	{
		return [
			new \Twig_SimpleFilter('position', [$this, 'positionFilter']),
			new \Twig_SimpleFilter('stick', [$this, 'stickFilter']),
			new \Twig_SimpleFilter('pager', [$this, 'pagerFilter'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * @param string $position
	 * @return string
	 */
	public function positionFilter(string $position = null)
	{
		switch ($position) {
			case PlayerProfile::POSITION_GK:
				return 'Вратарь';
			case PlayerProfile::POSITION_LB:
				return 'Левый защитник';
			case PlayerProfile::POSITION_RB:
				return 'Правый защитник';
			case PlayerProfile::POSITION_CF:
				return 'Центральный нападающий';
			case PlayerProfile::POSITION_LF:
				return 'Левый нападающий';
			case PlayerProfile::POSITION_RF:
				return 'Правый нападающий';
			default:
				return '';
		}
	}

	/**
	 * @param string $stick
	 * @return string
	 */
	public function stickFilter(string $stick = null)
	{
		switch ($stick) {
			case PlayerProfile::STICK_L:
				return 'Левый';
			case PlayerProfile::STICK_R:
				return 'Правый';
			default:
				return '';
		}
	}

	/**
	 * @param int $page
	 * @param Request $request
	 * @param string $pageParamName
	 * @return string
	 */
	public function pagerFilter(int $page, Request $request, string $pageParamName = 'page')
	{
		$baseUrl = str_replace('&amp;', '&', $request->getQueryString());
		parse_str($baseUrl, $qsVars);
		unset($qsVars[$pageParamName]);
		$url = '?' . $pageParamName . '=' . $page;
		if (count($qsVars) > 0) {
			$qsVars = array_reverse($qsVars);
			$url = '?' . http_build_query($qsVars) . '&' . $pageParamName . '=' . $page;
		}
		return $url;
	}
}