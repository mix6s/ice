<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:39
 */

namespace Domain\UseCase;


use Domain\DTO\Request\CreateSeasonTeamRequest;
use Domain\DTO\Response\CreateSeasonTeamResponse;
use Domain\Entity\SeasonTeam;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;

/**
 * Class CreateSeasonTeamUseCase
 * @package Domain\UseCase
 */
class CreateSeasonTeamUseCase
{
	use UseCaseTrait;

	/**
	 * @param CreateSeasonTeamRequest $request
	 * @return CreateSeasonTeamResponse
	 * @throws DomainException
	 */
	public function execute(CreateSeasonTeamRequest $request): CreateSeasonTeamResponse
	{
		try {
			$team = $this->getContainer()->getTeamRepository()->findById($request->getTeamId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('Team not found with id ' . $request->getTeamId());
		}

		try {
			$season = $this->getContainer()->getSeasonRepository()->findById($request->getSeasonId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('Season not found with id ' . $request->getSeasonId());
		}

		try {
			$coach = $this->getContainer()->getPlayerRepository()->findById($request->getCoachId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('Coach not found with id ' . $request->getCoachId());
		}

		try {
			$league = $this->getContainer()->getLeagueRepository()->findById($request->getLeagueId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('League not found with id ' . $request->getLeagueId());
		}

		try {
			$this->getContainer()->getSeasonTeamRepository()->findByTeamAndSeason($team, $season);
			throw new DomainException('SeasonTeam already exist');
		} catch (EntityNotFoundException $e) {}

		$id = $this->getContainer()->getSeasonTeamRepository()->getNextId();
		$seasonTeam = SeasonTeam::create($id, $team, $league, $season, $coach);
		$this->getContainer()->getSeasonTeamRepository()->save($seasonTeam);
		return new CreateSeasonTeamResponse($seasonTeam);
	}
}