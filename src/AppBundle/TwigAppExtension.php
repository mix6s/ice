<?php

namespace AppBundle;

use Domain\Entity\Game;
use Domain\Entity\PenaltyEvent;
use Domain\Entity\PlayOff;
use Domain\Entity\PlayOffItem;
use Domain\Entity\Season;
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
		return [
			"getCurrentSeasonStatistic" => new Twig_Function_Method($this, "getCurrentSeasonStatistic"),
			"getSeasonStatistic" => new Twig_Function_Method($this, "getSeasonStatistic"),
			"getSeasonTop" => new Twig_Function_Method($this, "getSeasonTop"),
			"getCurrentSeasonTop" => new Twig_Function_Method($this, "getCurrentSeasonTop")
		];
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
		return $this->getSeasonStatistic($season);
	}

	/**
	 * @return array
	 */
	public function getCurrentSeasonTop()
	{
		$currentSeasonId = $this->container->get('settings.manager')->getCurrentSeasonId();
		if (empty($currentSeasonId)) {
			return [];
		}
		$season = $this->container->get('domain.repository.season')->findById($currentSeasonId);
		return $this->getSeasonTop($season);
	}

	/**
	 * @param Season $season
	 * @return array
	 */
	public function getSeasonTop(Season $season)
	{
		$top = $this->container->get('app.statistic.aggregator')->getTopStatistic($season);
		return $top;
	}

	/**
	 * @param Season $season
	 * @return array
	 */
	public function getSeasonStatistic(Season $season)
	{
		$stat = $this->container->get('app.statistic.aggregator')->getSeasonStatistic($season);
		$em = $this->container->get('doctrine.orm.entity_manager');
		$qb = $em->createQueryBuilder();
		$qb
			->from('Domain:PlayOff', 'pl')
			->leftJoin('pl.season', 's')
			->leftJoin('pl.league', 'l')
			->leftJoin('l.metadata', 'lm')
			->leftJoin('Domain:PlayOffItem', 'pli', 'WITH', 'pli.playOff = pl.id')
			->leftJoin('Domain:Game', 'g', 'WITH', 'g.playOffItem = pli.id')
			->where('s.id = :season_id')
			->setParameter('season_id', $season->getId())
			->orderBy('s.year', 'DESC')
			->addOrderBy('lm.title','ASC')
			->addOrderBy('pli.rank','ASC')
			->addOrderBy('pli.id','ASC')
			->addOrderBy('g.datetime','ASC');

		$byLeague = [];
		foreach ($stat->getBeastsByLeagues() as $beastsByLeague)
		{
			if (!array_key_exists($beastsByLeague->getLeague()->getId(), $byLeague)) {
				$byLeague[$beastsByLeague->getLeague()->getId()] = [
					'league' => $beastsByLeague->getLeague(),
					'seasonteams' => [],
					'playoff' => null,
					'playoffItems' => [],
					'playoffGames' => [],
					'bests' => $beastsByLeague
				];
			}
		}
		$seasonTeamStatistics = $stat->getSeasonTeamStatistics();
		foreach ($seasonTeamStatistics as $item) {
			$byLeague[$item->getSeasonTeam()->getLeague()->getId()]['seasonteams'][] = $item;
		}

		$playoffs = array_values(array_filter($qb->select('pl')->getQuery()->getResult(), function ($item) {
			return $item !== null;
		}));
		/** @var PlayOff $playoff */
		foreach ($playoffs as $playoff) {
			$byLeague[$playoff->getLeague()->getId()]['playoff'] = $playoff;
		}

		$playoffsItems = array_values(array_filter($qb->select('pli')->getQuery()->getResult(), function ($item) {
			return $item !== null;
		}));
		/** @var PlayOffItem $item */
		foreach ($playoffsItems as $item) {
			if (!array_key_exists($item->getRank(), $byLeague[$item->getPlayOff()->getLeague()->getId()]['playoffItems'])) {
				$byLeague[$item->getPlayOff()->getLeague()->getId()]['playoffItems'][$item->getRank()] = [];
			}
			$byLeague[$item->getPlayOff()->getLeague()->getId()]['playoffItems'][$item->getRank()][] = $item;
		}

		$playoffsGames = array_values(array_filter($qb->select('g')->getQuery()->getResult(), function ($item) {
			return $item !== null;
		}));
		/** @var Game $game */
		foreach ($playoffsGames as $game) {
			if (!array_key_exists($game->getPlayOffItem()->getId(), $byLeague[$game->getPlayOffItem()->getPlayOff()->getLeague()->getId()]['playoffGames'])) {
				$byLeague[$game->getPlayOffItem()->getPlayOff()->getLeague()->getId()]['playoffGames'][$game->getPlayOffItem()->getId()] = [];
			}
			$byLeague[$game->getPlayOffItem()->getPlayOff()->getLeague()->getId()]['playoffGames'][$game->getPlayOffItem()->getId()][] = $game;
		}
		dump($byLeague);
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