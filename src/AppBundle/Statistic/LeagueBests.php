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
	 * @var \AppBundle\Statistic\SeasonTeamMember[]
	 */
	private $members = [];

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
	public function addMember(SeasonTeamMember $member)
	{
		$this->members[] = $member;
	}

	/**
	 * @return SeasonTeamMember[]
	 */
	public function getBestAssistantList(): array
	{
		$stats = $this->members;
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getAssistantGoals() < $memberB->getAssistantGoals()) {
				return 1;
			} elseif ($memberA->getAssistantGoals() === $memberB->getAssistantGoals()
				&& $memberA->getGamesCount() > $memberB->getGamesCount()
			) {
				return 1;
			}
			return -1;
		});
		return $stats;
	}

	/**
	 * @return SeasonTeamMember[]
	 */
	public function getBestForwardList(): array
	{
		$stats = $this->members;
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getForwardScore() < $memberB->getForwardScore()) {
				return 1;
			} elseif ($memberA->getForwardScore() === $memberB->getForwardScore()
				&& $memberA->getGoals() < $memberB->getGoals()
			) {
				return 1;
			} elseif ($memberA->getForwardScore() === $memberB->getForwardScore()
				&& $memberA->getGoals() === $memberB->getGoals()
				&& $memberA->getGamesCount() > $memberB->getGamesCount()
			) {
				return 1;
			}
			return -1;
		});
		return $stats;
	}

	/**
	 * @return SeasonTeamMember[]
	 */
	public function getBestSniperList(): array
	{
		$stats = $this->members;
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getGoals() < $memberB->getGoals()) {
				return 1;
			} elseif ($memberA->getGoals() === $memberB->getGoals()
				&& $memberA->getGamesCount() > $memberB->getGamesCount()
			) {
				return 1;
			}
			return -1;
		});
		return $stats;
	}

	/**
	 * @return SeasonTeamMember[]
	 */
	public function getBestBackList(): array
	{
		$stats = array_filter($this->members, function (SeasonTeamMember $member) {
			/** @var PlayerMetadata $playerMeta */
			$playerMeta = $member->getMember()->getPlayer()->getMetadata();
			return $playerMeta->isPositionBack();
		});
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getForwardScore() < $memberB->getForwardScore()) {
				return 1;
			} elseif ($memberA->getForwardScore() === $memberB->getForwardScore()
				&& $memberA ->getGoals() < $memberB->getGoals()
			) {
				return 1;
			} elseif ($memberA->getForwardScore() === $memberB->getForwardScore()
				&& $memberA->getGoals() === $memberB->getGoals()
				&& $memberA->getGamesCount() > $memberB->getGamesCount()
			) {
				return 1;
			}
			return -1;
		});
		return $stats;
	}

	/**
	 * @return SeasonTeamMember[]
	 */
	public function getBestGoalkeeperList(): array
	{
		$stats = array_filter($this->members, function (SeasonTeamMember $member) {
			/** @var PlayerMetadata $playerMeta */
			$playerMeta = $member->getMember()->getPlayer()->getMetadata();
			return $member->getTotalSecondsTime() > 0 && $playerMeta->isPositionGoalkeeper();
		});
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getReliabilityCoef() > $memberB->getReliabilityCoef()) {
				return 1;
			} elseif ($memberA->getReliabilityCoef() === $memberB->getReliabilityCoef()
				&& $memberA->getTotalSecondsTime() < $memberB->getTotalSecondsTime()
			) {
				return 1;
			}
			return -1;
		});
		return $stats;
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