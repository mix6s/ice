<?php

namespace Domain\UseCase;


use Domain\DTO\Request\CreatePlayOffGridItemRequest;
use Domain\DTO\Response\CreatePlayOffGridItemResponse;
use Domain\Entity\PlayOffItem;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;

class CreatePlayOffGridItemUseCase
{
	use UseCaseTrait;

	public function execute(CreatePlayOffGridItemRequest $request): CreatePlayOffGridItemResponse
	{
		try {
			$playOff = $this->getContainer()->getPlayOffRepository()->findById($request->getPlayoffId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('PlayOff not found with id ' . $request->getPlayoffId());
		}
		$teamA = null;
		$teamB = null;
		$winner = null;

		if ($request->getSeasonteamAId() !== null) {
			try {
				$teamA = $this->getContainer()->getSeasonTeamRepository()->findById($request->getSeasonteamAId());
			} catch (EntityNotFoundException $e) {
				throw new DomainException('SeasonTeam not found with id ' . $request->getSeasonteamAId());
			}
		}

		if ($request->getSeasonteamBId() !== null) {
			try {
				$teamB = $this->getContainer()->getSeasonTeamRepository()->findById($request->getSeasonteamBId());
			} catch (EntityNotFoundException $e) {
				throw new DomainException('SeasonTeam not found with id ' . $request->getSeasonteamBId());
			}
		}
		if ($request->getWinnerId() !== null) {
			try {
				$winner = $this->getContainer()->getSeasonTeamRepository()->findById($request->getWinnerId());
			} catch (EntityNotFoundException $e) {
				throw new DomainException('SeasonTeam not found with id ' . $request->getWinnerId());
			}
		}

		$id = $this->getContainer()->getPlayOffItemRepository()->getNextId();
		$item = new PlayOffItem($id, $playOff, $request->getRank(), $teamA, $teamB, $winner);
		$this->getContainer()->getPlayOffItemRepository()->save($item);
		return new CreatePlayOffGridItemResponse($item);
	}
}