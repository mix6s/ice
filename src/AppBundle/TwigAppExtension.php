<?php

namespace AppBundle;

use Domain\Entity\Game;
use Domain\Entity\PenaltyEvent;
use Domain\Entity\SeasonTeam;
use Domain\Entity\SeasonTeamMember;
use DomainBundle\Entity\PlayerMetadata;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig_Function_Method;


/**
 * Class TwigAppExtension
 * @package AppBundle
 */
class TwigAppExtension extends \Twig_Extension
{
	private $scoresByGame = [];
	private $container;

	/**
	 * TwigAppExtension constructor.
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function getFunctions()
	{
		return array(
			"getCurrentSeasonStatistic" => new Twig_Function_Method($this, "getCurrentSeasonStatistic")
		);
	}

	/**
	 * @return array
	 */
	public function getFilters()
	{
		return [
			new \Twig_SimpleFilter('position', [$this, 'positionFilter']),
			new \Twig_SimpleFilter('stick', [$this, 'stickFilter']),
			new \Twig_SimpleFilter('scoreA', [$this, 'scoreA']),
			new \Twig_SimpleFilter('scoreB', [$this, 'scoreB']),
			new \Twig_SimpleFilter('pager', [$this, 'pagerFilter'], ['is_safe' => ['html']]),
			new \Twig_SimpleFilter('eventPeriod', [$this, 'eventPeriod']),
			new \Twig_SimpleFilter('penaltyPeriod', [$this, 'penaltyPeriod']),
			new \Twig_SimpleFilter('eventPeriodType', [$this, 'eventPeriodType']),
			new \Twig_SimpleFilter('gameDatetime', [$this, 'gameDatetimeFilter']),
			new \Twig_SimpleFilter('memberStatistic', [$this, 'memberStatistic']),
			new \Twig_SimpleFilter('seasonTeamStatistic', [$this, 'seasonTeamStatistic']),
			new \Twig_SimpleFilter('gameStatistic', [$this, 'gameStatistic']),
		];
	}

	/**
	 * @return array
	 */
	public function getCurrentSeasonStatistic()
	{
		$currentSeasonId = $this->container->get('settings.manager')->getCurrentSeasonId();
		if (empty($currentSeasonId)) {
			return [];
		}
		$season = $this->container->get('domain.repository.season')->findById($currentSeasonId);
		$stat = $this->container->get('app.statistic.aggregator')->getSeasonStatistic($season);
		$byLeague = [];
		foreach ($stat as $item) {
			if (!array_key_exists($item->getSeasonTeam()->getLeague()->getId(), $byLeague)) {
				$byLeague[$item->getSeasonTeam()->getLeague()->getId()] = [
					'league' => $item->getSeasonTeam()->getLeague(),
					'seasonteams' => []
				];
			}
			$byLeague[$item->getSeasonTeam()->getLeague()->getId()]['seasonteams'][] = $item;
		}
		return $byLeague;
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

	/**
	 * @param int $secondsInterval
	 * @return string
	 */
	public function eventPeriod(int $secondsInterval)
	{
		$minutes = (int)($secondsInterval / 60);
		$seconds = $secondsInterval - $minutes * 60;
		return ($minutes < 10 ? '0' . $minutes : $minutes) . ':' . ($seconds < 10 ? '0' . $seconds : $seconds);
	}

	/**
	 * @param string $type
	 * @return string
	 */
	public function penaltyPeriod(string $type)
	{
		switch ($type) {
			case PenaltyEvent::PENALTY_TIME_TYPE_2:
				return '2 мин';
			case PenaltyEvent::PENALTY_TIME_TYPE_2_2:
				return '2 + 2 мин';
			case PenaltyEvent::PENALTY_TIME_TYPE_10:
				return '10 мин';
			case PenaltyEvent::PENALTY_TIME_TYPE_5_20:
				return '5 + 20 мин';
		}
		return '';
	}

	/**
	 * @param int $secondsInterval
	 * @return int
	 */
	public function eventPeriodType(int $secondsInterval)
	{
		$num = (int)($secondsInterval / (20 * 60));
		return $num > 4 ? 4 : $num;
	}

	/**
	 * @param Game $game
	 * @return mixed
	 */
	public function scoreA(Game $game)
	{
		$score = $this->container->get('app.policy.game_score_policy')->scoreA($game);
		return $score === null ? '-' : $score;
	}

	/**
	 * @param Game $game
	 * @return mixed
	 */
	public function scoreB(Game $game)
	{
		$score = $this->container->get('app.policy.game_score_policy')->scoreB($game);
		return $score === null ? '-' : $score;
	}

	/**
	 * @param SeasonTeamMember $member
	 * @return Statistic\SeasonTeamMember
	 */
	public function memberStatistic(SeasonTeamMember $member): \AppBundle\Statistic\SeasonTeamMember
	{
		return $this->container->get('app.statistic.aggregator')->getSeasonTeamMemberStatistic($member);
	}

	/**
	 * @param SeasonTeam $seasonTeam
	 * @return Statistic\SeasonTeam
	 */
	public function seasonTeamStatistic(SeasonTeam $seasonTeam): \AppBundle\Statistic\SeasonTeam
	{
		return $this->container->get('app.statistic.aggregator')->getSeasonTeamStatistic($seasonTeam);
	}

	/**
	 * @param Game $game
	 * @return Statistic\Game
	 */
	public function gameStatistic(Game $game): \AppBundle\Statistic\Game
	{
		return $this->container->get('app.statistic.aggregator')->getGameStatistic($game);
	}
}