<?php

namespace Domain\UseCase;


use Domain\DTO\Request\SavePlayOffItemRequest;
use Domain\DTO\Response\CreatePlayOffGridItemResponse;
use Domain\Entity\PlayOffItem;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;

class SavePlayOffItemUseCase
{
	use UseCaseTrait;

	public function execute(SavePlayOffItemRequest $request): CreatePlayOffGridItemResponse
	{
		if ($request->hasId()) {
			$item = $this->getContainer()->getPlayOffItemRepository()->findById($request->getId());
		} else {
			try {
				$playOff = $this->getContainer()->getPlayOffRepository()->findById($request->getPlayoffId());
			} catch (EntityNotFoundException $e) {
				throw new DomainException('PlayOff not found with id ' . $request->getPlayoffId());
			}
			$id = $this->getContainer()->getPlayOffItemRepository()->getNextId();
			$item = new PlayOffItem($id, $playOff, $request->getRank());
		}
		if ($request->getSeasonteamAId() !== null) {
			try {
				$teamA = $this->getContainer()->getSeasonTeamRepository()->findById($request->getSeasonteamAId());
				$item->setSeasonTeamA($teamA);
			} catch (EntityNotFoundException $e) {
				throw new DomainException('SeasonTeam not found with id ' . $request->getSeasonteamAId());
			}
		}

		if ($request->getSeasonteamBId() !== null) {
			try {
				$teamB = $this->getContainer()->getSeasonTeamRepository()->findById($request->getSeasonteamBId());
				$item->setSeasonTeamB($teamB);
			} catch (EntityNotFoundException $e) {
				throw new DomainException('SeasonTeam not found with id ' . $request->getSeasonteamBId());
			}
		}
		if ($request->getWinnerId() !== null) {
			try {
				$winner = $this->getContainer()->getSeasonTeamRepository()->findById($request->getWinnerId());
				$item->setWinner($winner);
			} catch (EntityNotFoundException $e) {
				throw new DomainException('SeasonTeam not found with id ' . $request->getWinnerId());
			}
		}
		$this->getContainer()->getPlayOffItemRepository()->save($item);
		return new CreatePlayOffGridItemResponse($item);
	}
}