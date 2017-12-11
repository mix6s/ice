<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 19.11.2017
 * Time: 21:05
 */

namespace AppBundle\Statistic;


use Domain\Entity\League;

/**
 * Class Season
 * @package AppBundle\Statistic
 */
class Season
{
	private $seasonTeamStatistics = [];
	private $bestsByLeague = [];

	/**
	 * @var \Domain\Entity\Season
	 */
	private $season;

	private $sortContext = [];

	/**
	 * Season constructor.
	 * @param \Domain\Entity\Season $season
	 */
	public function __construct(\Domain\Entity\Season $season)
	{
		$this->season = $season;
	}

	/**
	 * @param League $league
	 * @return LeagueBests
	 */
	public function getBestsByLeague(League $league): LeagueBests
	{
		if (!array_key_exists($league->getId(), $this->bestsByLeague)) {
			return new LeagueBests($league);
		}
		return $this->bestsByLeague[$league->getId()];
	}

	/**
	 * @param LeagueBests $leagueBests
	 */
	public function setBestsByLeague(LeagueBests $leagueBests)
	{
		$this->bestsByLeague[$leagueBests->getLeague()->getId()] = $leagueBests;
	}

	/**
	 * @return LeagueBests[]
	 */
	public function getBeastsByLeagues(): array
	{
		return $this->bestsByLeague;
	}

	/**
	 * @return \AppBundle\Statistic\SeasonTeam[]
	 */
	public function getSeasonTeamStatistics(): array
	{
		$stats = $this->seasonTeamStatistics;
		usort($stats, [$this, 'sortSeasonTeams']);

		$byScore = [];
		/** @var \AppBundle\Statistic\SeasonTeam $stat */
		foreach ($stats as $stat) {
			$score = $stat->getScores();
			if (!array_key_exists($score, $byScore)) {
				$byScore[$score] = [];
			}
			$byScore[$score][] = $stat;
		}
		$sorted = [];
		foreach ($byScore as $score => $items) {
			if (count($items) > 1) {
				$context = [];
				/** @var \AppBundle\Statistic\SeasonTeam $item */
				foreach ($items as $item) {
					$context[] = $item->getSeasonTeam()->getId();
				}
				SeasonTeam::$context = $context;
				usort($items, [$this, 'sortSeasonTeams']);
			}
			foreach ($items as $item) {
				$sorted[] = $item;
			}
		}
		SeasonTeam::$context = [];
		return $sorted;
	}

	/**
	 * @param SeasonTeam $seasonTeam
	 */
	public function setSeasonTeamStatistic(SeasonTeam $seasonTeam)
	{
		$this->seasonTeamStatistics[$seasonTeam->getSeasonTeam()->getId()] = $seasonTeam;
	}

	/**
	 * @param \Domain\Entity\SeasonTeam $seasonTeam
	 * @return SeasonTeam
	 */
	public function getSeasonTeamStatistic(\Domain\Entity\SeasonTeam $seasonTeam): SeasonTeam
	{
		if (!array_key_exists($seasonTeam->getId(), $this->seasonTeamStatistics)) {
			return new SeasonTeam($seasonTeam);
		}
		return $this->seasonTeamStatistics[$seasonTeam->getId()];
	}

	/**
	 * @param \AppBundle\Statistic\SeasonTeam $teamA
	 * @param \AppBundle\Statistic\SeasonTeam $teamB
	 * @return int
	 */
	private function sortSeasonTeams(\AppBundle\Statistic\SeasonTeam $teamA, \AppBundle\Statistic\SeasonTeam $teamB)
	{

		//набравшая наибольшее количество очков во всех матчах;
		if ($teamA->getScores() < $teamB->getScores()) {
			return 1;
		} elseif ($teamA->getScores() > $teamB->getScores()) {
			return -1;
		}

		//набравшая наибольшее количество очков во всех матчах между собой;
		if ($teamA->getScores($teamB->getSeasonTeam()) < $teamB->getScores($teamA->getSeasonTeam())) {
			return 1;
		} elseif ($teamA->getScores($teamB->getSeasonTeam()) > $teamB->getScores($teamA->getSeasonTeam())) {
			return -1;
		}

		//имеющая лучшую разницу забитых и пропущенных шайб во всех играх между этими командами;
		if ($teamA->getGoals($teamB->getSeasonTeam()) < $teamB->getGoals($teamA->getSeasonTeam())) {
			return 1;
		} elseif ($teamA->getGoals($teamB->getSeasonTeam()) > $teamB->getGoals($teamA->getSeasonTeam())) {
			return -1;
		}

		//имеющая лучшую разницу забитых и пропущенных шайб во всех матчах;
		if ($teamA->getGoals() - $teamA->getGoalsFailed() < $teamB->getGoals() - $teamB->getGoalsFailed()) {
			return 1;
		} elseif ($teamA->getGoals() - $teamA->getGoalsFailed() > $teamB->getGoals() - $teamB->getGoalsFailed()) {
			return -1;
		}

		//имеющая лучшее соотношение забитых и пропущенных шайб во всех матчах;
		if ($teamA->getGoalsFailed() !== 0 && $teamB->getGoalsFailed() !== 0) {
			if ($teamA->getGoals() / $teamA->getGoalsFailed() < $teamB->getGoals() / $teamB->getGoalsFailed()) {
				return 1;
			} elseif ($teamA->getGoals() / $teamA->getGoalsFailed() > $teamB->getGoals() / $teamB->getGoalsFailed()) {
				return -1;
			}
		}


		//имеющая наибольшее число побед во всех матчах;
		if ($teamA->getWinCount() < $teamB->getWinCount()) {
			return 1;
		} elseif ($teamA->getWinCount() > $teamB->getWinCount()) {
			return -1;
		}

		//забросившая наибольшее количество шайб во всех матчах этапа.
		if ($teamA->getGoals() < $teamB->getGoals()) {
			return 1;
		} elseif ($teamA->getGoals() > $teamB->getGoals()) {
			return -1;
		}
		return 0;
	}
}