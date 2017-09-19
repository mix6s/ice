<?php

namespace AppBundle;

use DomainBundle\Entity\PlayerMetadata;
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
			new \Twig_SimpleFilter('gameDatetime', [$this, 'gameDatetimeFilter']),
		];
	}

	/**
	 * @param string $position
	 * @return string
	 */
	public function positionFilter(string $position = null)
	{
		switch ($position) {
			case PlayerMetadata::POSITION_GK:
				return 'Вратарь';
			case PlayerMetadata::POSITION_LB:
				return 'Левый защитник';
			case PlayerMetadata::POSITION_RB:
				return 'Правый защитник';
			case PlayerMetadata::POSITION_CF:
				return 'Центральный нападающий';
			case PlayerMetadata::POSITION_LF:
				return 'Левый нападающий';
			case PlayerMetadata::POSITION_RF:
				return 'Правый нападающий';
			default:
				return '';
		}
	}

	/**
	 * @param \DateTime $date
	 * @return string
	 */
	public function gameDatetimeFilter(\DateTime $date)
	{
		$months = [
			'января',
			'февраля',
			'марта',
			'апреля',
			'мая',
			'июня',
			'июля',
			'августа',
			'сентября',
			'октября',
			'ноября',
			'декабря'
		];
		return sprintf($date->format('d ' . $months[$date->format('n') - 1]. ' Y H:i'));
	}

	/**
	 * @param string $stick
	 * @return string
	 */
	public function stickFilter(string $stick = null)
	{
		switch ($stick) {
			case PlayerMetadata::STICK_L:
				return 'Левый';
			case PlayerMetadata::STICK_R:
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