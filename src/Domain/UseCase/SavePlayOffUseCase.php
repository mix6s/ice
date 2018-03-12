<?php

namespace Domain\UseCase;


use Domain\DTO\Request\SavePlayOffRequest;
use Domain\DTO\Response\CreatePlayOffResponse;
use Domain\Entity\PlayOff;
use Domain\Exception\EntityNotFoundException;
use DomainException;

class SavePlayOffUseCase
{
	use UseCaseTrait;

	public function execute(SavePlayOffRequest $request): CreatePlayOffResponse
	{
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

		if ($request->hasId()) {
			$playoff = $this->getContainer()->getPlayOffRepository()->findById($request->getId());
			$playoff->setStartAt($request->getStartAt());
		} else {
			try {
				$playoff = $this->getContainer()->getPlayOffRepository()->findBySeasonAndLeague($season->getId(), $league->getId());
				throw new DomainException('PlayOff already exist');
			} catch (EntityNotFoundException $e) {
				$id = $this->getContainer()->getPlayOffRepository()->getNextId();
				$playoff = new PlayOff($id, $season, $league, $request->getStartAt());
			}
		}

		$this->getContainer()->getPlayOffRepository()->save($playoff);
		return new CreatePlayOffResponse($playoff);
	}
}