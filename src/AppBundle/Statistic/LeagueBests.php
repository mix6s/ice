<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 19.11.2017
 * Time: 19:48
 */

namespace AppBundle\Statistic;

use Domain\Entity\League;
use Domain\ValueObject\GameType;
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
	 * @var League
	 */
	private $league;

	/**
	 * @var \AppBundle\Statistic\SeasonTeamMember[]
	 */
	private $members = [];
	/**
	 * @var GameType|null
	 */
	private $type;

	/**
	 * LeagueBests constructor.
	 * @param League $league
	 */
	public function __construct(League $league, GameType $type = null)
	{
		$this->league = $league;
		$this->type = $type;
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
		} elseif ($this->bestAssistant->getAssistantGoals($this->type) < $bestAssistant->getAssistantGoals($this->type)) {
			$this->bestAssistant = $bestAssistant;
		} elseif ($this->bestAssistant->getAssistantGoals($this->type) === $bestAssistant->getAssistantGoals($this->type)
			&& $this->bestAssistant->getGamesCount($this->type) > $bestAssistant->getGamesCount($this->type)
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
		} elseif ($this->bestForward->getForwardScore($this->type) < $bestForward->getForwardScore($this->type)) {
			$this->bestForward = $bestForward;
		} elseif ($this->bestForward->getForwardScore($this->type) === $bestForward->getForwardScore($this->type)
			&& $this->bestForward->getGoals($this->type) < $bestForward->getGoals($this->type)
		) {
			$this->bestForward = $bestForward;
		} elseif ($this->bestForward->getForwardScore($this->type) === $bestForward->getForwardScore($this->type)
			&& $this->bestForward->getGoals($this->type) === $bestForward->getGoals($this->type)
			&& $this->bestForward->getGamesCount($this->type) > $bestForward->getGamesCount($this->type)
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
		} elseif ($this->bestSniper->getGoals($this->type) < $bestSniper->getGoals($this->type)) {
			$this->bestSniper = $bestSniper;
		} elseif ($this->bestSniper->getGoals($this->type) === $bestSniper->getGoals($this->type)
			&& $this->bestSniper->getGamesCount($this->type) > $bestSniper->getGamesCount($this->type)
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
		} elseif ($this->bestBack ->getForwardScore($this->type) < $bestBack->getForwardScore($this->type)) {
			$this->bestBack  = $bestBack;
		} elseif ($this->bestBack ->getForwardScore($this->type) === $bestBack->getForwardScore($this->type)
			&& $this->bestBack ->getGoals($this->type) < $bestBack->getGoals($this->type)
		) {
			$this->bestBack  = $bestBack;
		} elseif ($this->bestBack ->getForwardScore($this->type) === $bestBack->getForwardScore($this->type)
			&& $this->bestBack ->getGoals($this->type) === $bestBack->getGoals($this->type)
			&& $this->bestBack ->getGamesCount($this->type) > $bestBack->getGamesCount($this->type)
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
		} elseif ($this->bestGoalkeeper->getReflectedBulletsPercent($this->type) < $bestGoalkeeper->getReflectedBulletsPercent($this->type)) {
			$this->bestGoalkeeper = $bestGoalkeeper;
		} elseif ($this->bestGoalkeeper->getReflectedBulletsPercent($this->type) === $bestGoalkeeper->getReflectedBulletsPercent($this->type)
			&& $this->bestGoalkeeper->getTotalSecondsTime($this->type) < $bestGoalkeeper->getTotalSecondsTime($this->type)
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

	public function getBestPenaltyList(): array
	{
		$stats = array_filter($this->members, function (SeasonTeamMember $member) {
			return !empty($member->getPenaltyTime($this->type));
		});
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getPenaltyTime($this->type) < $memberB->getPenaltyTime($this->type)) {
				return 1;
			}
			return -1;
		});
		return $stats;
	}

	/**
	 * @return SeasonTeamMember[]
	 */
	public function getBestAssistantList(): array
	{
		$stats = array_filter($this->members, function (SeasonTeamMember $member) {
			/** @var PlayerMetadata $playerMeta */
			$playerMeta = $member->getMember()->getPlayer()->getMetadata();
			return !$playerMeta->isPositionGoalkeeper();
		});
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getAssistantGoals($this->type) < $memberB->getAssistantGoals($this->type)) {
				return 1;
			} elseif ($memberA->getAssistantGoals($this->type) === $memberB->getAssistantGoals($this->type)
				&& $memberA->getGamesCount($this->type) > $memberB->getGamesCount($this->type)
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
		$stats = array_filter($this->members, function (SeasonTeamMember $member) {
			/** @var PlayerMetadata $playerMeta */
			$playerMeta = $member->getMember()->getPlayer()->getMetadata();
			return !$playerMeta->isPositionGoalkeeper();
		});
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getForwardScore($this->type) < $memberB->getForwardScore($this->type)) {
				return 1;
			} elseif ($memberA->getForwardScore($this->type) === $memberB->getForwardScore($this->type)
				&& $memberA->getGoals($this->type) < $memberB->getGoals($this->type)
			) {
				return 1;
			} elseif ($memberA->getForwardScore($this->type) === $memberB->getForwardScore($this->type)
				&& $memberA->getGoals($this->type) === $memberB->getGoals($this->type)
				&& $memberA->getGamesCount($this->type) > $memberB->getGamesCount($this->type)
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
		$stats = array_filter($this->members, function (SeasonTeamMember $member) {
			/** @var PlayerMetadata $playerMeta */
			$playerMeta = $member->getMember()->getPlayer()->getMetadata();
			return !$playerMeta->isPositionGoalkeeper();
		});
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getGoals($this->type) < $memberB->getGoals($this->type)) {
				return 1;
			} elseif ($memberA->getGoals($this->type) === $memberB->getGoals($this->type)
				&& $memberA->getGamesCount($this->type) > $memberB->getGamesCount($this->type)
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
			if ($memberA->getForwardScore($this->type) < $memberB->getForwardScore($this->type)) {
				return 1;
			} elseif ($memberA->getForwardScore($this->type) === $memberB->getForwardScore($this->type)
				&& $memberA ->getGoals($this->type) < $memberB->getGoals($this->type)
			) {
				return 1;
			} elseif ($memberA->getForwardScore($this->type) === $memberB->getForwardScore($this->type)
				&& $memberA->getGoals($this->type) === $memberB->getGoals($this->type)
				&& $memberA->getGamesCount($this->type) > $memberB->getGamesCount($this->type)
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
			return $member->getTotalSecondsTime($this->type) > 0 && $playerMeta->isPositionGoalkeeper() && $member->getGamesCountAsGoalkeeper($this->type) >= 8;
		});
		usort($stats, function (SeasonTeamMember $memberA, SeasonTeamMember $memberB) {
			if ($memberA->getReflectedBulletsPercent($this->type) < $memberB->getReflectedBulletsPercent($this->type)) {
				return 1;
			} elseif ($memberA->getReflectedBulletsPercent($this->type) === $memberB->getReflectedBulletsPercent($this->type)
				&& $memberA->getTotalSecondsTime($this->type) < $memberB->getTotalSecondsTime($this->type)
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
		if ($playerMeta->isPositionGoalkeeper() && $member->getTotalSecondsTime() > 0 && $member->getGamesCountAsGoalkeeper() >= 8) {
			$this->setBestGoalkeeper($member);
		}
	}
	/**
	 * @return League
	 */
	public function getLeague(): League
	{
		return $this->league;
	}

}