<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 22.09.2017
 * Time: 13:20
 */

namespace Domain\UseCase;


use Domain\DTO\GoalEventData;
use Domain\DTO\PenaltyEventData;
use Domain\DTO\Request\SaveGameEventsRequest;
use Domain\Entity\GameEvent;
use Domain\Entity\GoalEvent;
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
		foreach ($request->getGameEventsData() as $eventData) {
			$eventId = $this->getContainer()->getGameEventRepository()->getNextId();
			if ($eventData instanceof GoalEventData) {
				$member = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getMemberId());
				if ($eventData->getAssistantAId() !== null) {
					$assistantA = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getAssistantAId());
				} else {
					$assistantA = null;
				}

				if ($eventData->getAssistantBId() !== null) {
					$assistantB = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getAssistantBId());
				} else {
					$assistantB = null;
				}
				$secondsFromStart = $eventData->getSecondsFromStart();
				$event = new GoalEvent($eventId, $game, $secondsFromStart, $member, $assistantA, $assistantB);
			} elseif ($eventData instanceof PenaltyEventData) {
				$member = $this->getContainer()->getSeasonTeamMemberRepository()->findById($eventData->getMemberId());
				$secondsFromStart = $eventData->getSecondsFromStart();
				$penaltyTimeType = $eventData->getPenaltyTimeType();
				$event = new PenaltyEvent($eventId, $game, $secondsFromStart, $member, $penaltyTimeType);
			} else {
				throw new DomainException("Unknown game event data type");
			}
			$this->getContainer()->getGameEventRepository()->save($event);
			$events[] = $event;
		}
		return $events;
	}
}