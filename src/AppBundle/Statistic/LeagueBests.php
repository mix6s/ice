<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 19.11.2017
 * Time: 19:48
 */

namespace AppBundle\Statistic;

use DomainBundle\Entity\PlayerMetadata;


/**
 * Class LeagueBests
 * @package AppBundle\Statistic
 */
class LeagueBests
{
	/** @var  \AppBundle\Statistic\SeasonTeamMember */
	private $bestAssistant;
	/** @var  \AppBundle\Statistic\SeasonTeamMember */
	private $bestForward;
	/** @var  \AppBundle\Statistic\SeasonTeamMember */
	private $bestSniper;
	/** @var  \AppBundle\Statistic\SeasonTeamMember */
	private $bestBack;
	/** @var  \AppBundle\Statistic\SeasonTeamMember */
	private $bestGoalkeeper;
	/**
	 * @var \Domain\Entity\League
	 */
	private $league;

	/**
	 * LeagueBests constructor.
	 * @param \Domain\Entity\League $league
	 */
	public function __construct(\Domain\Entity\League $league)
	{

		$this->league = $league;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getBestAssistant()
	{
		return $this->bestAssistant;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getBestForward()
	{
		return $this->bestForward;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getBestSniper()
	{
		return $this->bestSniper;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getBestBack()
	{
		return $this->bestBack;
	}

	/**
	 * @return SeasonTeamMember
	 */
	public function getBestGoalkeeper()
	{
		return $this->bestGoalkeeper;
	}

	/**
	 * @param SeasonTeamMember $bestAssistant
	 */
	public function setBestAssistant(SeasonTeamMember $bestAssistant)
	{
		if ($this->bestAssistant === null) {
			$this->bestAssistant = $bestAssistant;
		} elseif ($this->bestAssistant->getAssistantGoals() < $bestAssistant->getAssistantGoals()) {
			$this->bestAssistant = $bestAssistant;
		} elseif ($this->bestAssistant->getAssistantGoals() === $bestAssistant->getAssistantGoals()
			&& $this->bestAssistant->getGamesCount() > $bestAssistant->getGamesCount()
		) {
			$this->bestAssistant = $bestAssistant;
		}
	}

	/**
	 * @param SeasonTeamMember $bestForward
	 */
	public function setBestForward(SeasonTeamMember $bestForward)
	{
		if ($this->bestForward === null) {
			$this->bestForward = $bestForward;
		} elseif ($this->bestForward->getForwardScore() < $bestForward->getForwardScore()) {
			$this->bestForward = $bestForward;
		} elseif ($this->bestForward->getForwardScore() === $bestForward->getForwardScore()
			&& $this->bestForward->getGoals() < $bestForward->getGoals()
		) {
			$this->bestForward = $bestForward;
		} elseif ($this->bestForward->getForwardScore() === $bestForward->getForwardScore()
			&& $this->bestForward->getGoals() === $bestForward->getGoals()
			&& $this->bestForward->getGamesCount() > $bestForward->getGamesCount()
		) {
			$this->bestForward = $bestForward;
		}
	}

	/**
	 * @param SeasonTeamMember $bestSniper
	 */
	public function setBestSniper(SeasonTeamMember $bestSniper)
	{
		if ($this->bestSniper === null) {
			$this->bestSniper = $bestSniper;
		} elseif ($this->bestSniper->getGoals() < $bestSniper->getGoals()) {
			$this->bestSniper = $bestSniper;
		} elseif ($this->bestSniper->getGoals() === $bestSniper->getGoals()
			&& $this->bestSniper->getGamesCount() > $bestSniper->getGamesCount()
		) {
			$this->bestSniper = $bestSniper;
		}
	}

	/**
	 * Check before $playerMeta->isPositionBack()
	 * @param SeasonTeamMember $bestBack
	 */
	public function setBestBack(SeasonTeamMember $bestBack)
	{
		if ($this->bestBack === null) {
			$this->bestBack  = $bestBack;
		} elseif ($this->bestBack ->getForwardScore() < $bestBack->getForwardScore()) {
			$this->bestBack  = $bestBack;
		} elseif ($this->bestBack ->getForwardScore() === $bestBack->getForwardScore()
			&& $this->bestBack ->getGoals() < $bestBack->getGoals()
		) {
			$this->bestBack  = $bestBack;
		} elseif ($this->bestBack ->getForwardScore() === $bestBack->getForwardScore()
			&& $this->bestBack ->getGoals() === $bestBack->getGoals()
			&& $this->bestBack ->getGamesCount() > $bestBack->getGamesCount()
		) {
			$this->bestBack  = $bestBack;
		}
	}

	/**
	 * @param SeasonTeamMember $bestGoalkeeper
	 */
	public function setBestGoalkeeper(SeasonTeamMember $bestGoalkeeper)
	{
		if ($this->bestGoalkeeper === null) {
			$this->bestGoalkeeper = $bestGoalkeeper;
		} elseif ($this->bestGoalkeeper->getReliabilityCoef() > $bestGoalkeeper->getReliabilityCoef()) {
			$this->bestGoalkeeper = $bestGoalkeeper;
		} elseif ($this->bestGoalkeeper->getReliabilityCoef() === $bestGoalkeeper->getReliabilityCoef()
			&& $this->bestGoalkeeper->getTotalSecondsTime() < $bestGoalkeeper->getTotalSecondsTime()
		) {
			$this->bestGoalkeeper = $bestGoalkeeper;
		}
	}

	/**
	 * @param SeasonTeamMember $member
	 */
	public function nominate(SeasonTeamMember $member)
	{
		/** @var PlayerMetadata $playerMeta */
		$playerMeta = $member->getMember()->getPlayer()->getMetadata();
		$this->setBestAssistant($member);
		$this->setBestForward($member);
		$this->setBestSniper($member);
		if ($playerMeta->isPositionBack()) {
			$this->setBestBack($member);
		}
		if ($playerMeta->isPositionGoalkeeper() && $member->getTotalSecondsTime() > 0) {
			$this->setBestGoalkeeper($member);
		}
	}
	/**
	 * @return \Domain\Entity\League
	 */
	public function getLeague(): \Domain\Entity\League
	{
		return $this->league;
	}

}