<?php

namespace Domain\UseCase;


use Domain\DTO\Request\CreatePlayOffRequest;
use Domain\DTO\Response\CreatePlayOffResponse;
use Domain\Entity\PlayOff;
use Domain\Exception\EntityNotFoundException;
use DomainException;

class CreatePlayOffUseCase
{
	use UseCaseTrait;

	public function execute(CreatePlayOffRequest $request): CreatePlayOffResponse
	{
		$id = $this->getContainer()->getPlayOffRepository()->getNextId();
		try {
			$season = $this->getContainer()->getSeasonRepository()->findById($request->getSeasonId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('Season not found with id ' . $request->getSeasonId());
		}

		try {
			$league = $this->getContainer()->getLeagueRepository()->findById($request->getLeagueId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('League not found with id ' . $request->getLeagueId());
		}

		try {
			$playoff = $this->getContainer()->getPlayOffRepository()->findBySeasonAndLeague($season->getId(), $league->getId());
			throw new DomainException('PlayOff already exist');
		} catch (EntityNotFoundException $e) {
			$playoff = new PlayOff($id, $season, $league, $request->getStartAt());
		}

		$this->getContainer()->getPlayOffRepository()->save($playoff);
		return new CreatePlayOffResponse($playoff);
	}
}