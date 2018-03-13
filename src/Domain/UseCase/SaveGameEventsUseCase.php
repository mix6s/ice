<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:20
 */

namespace Domain\UseCase;


use Domain\DTO\GoalEventData;
use Domain\DTO\GoalkeeperData;
use Domain\DTO\PenaltyEventData;
use Domain\DTO\Request\SaveGameEventsRequest;
use Domain\Entity\GameEvent;
use Domain\Entity\GoalEvent;
use Domain\Entity\GoalkeeperEvent;
use Domain\Entity\PenaltyEvent;
use Domain\Exception\DomainException;

/**
 * Class SaveGameEventsUseCase
 * @package Domain\UseCase
 */
class SaveGameEventsUseCase
{
	use UseCaseTrait;

	/**
	 * @param SaveGameEventsRequest $request
	 * @return GameEvent[]
	 * @throws DomainException
	 */
	public function execute(SaveGameEventsRequest $request)
	{
		$game = $this->getContainer()->getGameRepository()->findById($request->getGameId());

		$oldEvents = $this->getContainer()->getGameEventRepository()->findByGame($game);
		foreach ($oldEvents as $event) {
			$this->getContainer()->getGameEventRepository()->remove($event);
		}
		$events = [];
		$scoreA = 0;
		$scoreB = 0;
		foreach ($request->getGameEventsData() as $eventData) {
			$eventId = $this->getContainer()->getGameEventRepository()->getNextId();
			if ($eventData instanceof GoalEventData) {
				$member = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getMemberId());
				if ($eventData->hasAssistantAId()) {
					$assistantA = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getAssistantAId());
				} else {
					$assistantA = null;
				}

				if ($eventData->hasAssistantBId()) {
					$assistantB = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getAssistantBId());
				} else {
					$assistantB = null;
				}
				$secondsFromStart = $eventData->getSecondsFromStart();
				$event = new GoalEvent($eventId, $eventData->getPeriod(), $game, $secondsFromStart, $member, $assistantA, $assistantB);
				if ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamA()->getId()) {
					$scoreA++;
				} elseif ($event->getMember()->getSeasonTeam()->getId() === $game->getSeasonTeamB()->getId()) {
					$scoreB++;
				}
			} elseif ($eventData instanceof GoalkeeperData) {
				$member = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getMemberId());
				$event = new GoalkeeperEvent($eventId, $game, $eventData->getBullets(), $eventData->getGoals(), $eventData->getDuration(), $member);
			} elseif ($eventData instanceof PenaltyEventData) {
				$member = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getMemberId());
				$secondsFromStart = $eventData->getSecondsFromStart();
				$penaltyTimeType = $eventData->getPenaltyTimeType();
				$event = new PenaltyEvent($eventId, $eventData->getPeriod(), $game, $secondsFromStart, $member, $penaltyTimeType);
			} else {
				throw new DomainException("Unknown game event data type");
			}
			$this->getContainer()->getGameEventRepository()->save($event);
			$events[] = $event;
		}
		$game->setScoreA($scoreA);
		$game->setScoreB($scoreB);
		$this->getContainer()->getGameRepository()->save($game);
		return $events;
	}
}