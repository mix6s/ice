<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:08
 */

namespace Domain\UseCase;


use Domain\DTO\Request\AddSeasonTeamMemberRequest;
use Domain\DTO\Response\AddSeasonTeamMemberResponse;
use Domain\Entity\SeasonTeamMember;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;

/**
 * Class AddSeasonTeamMemberUseCase
 * @package Domain\UseCase
 */
class AddSeasonTeamMemberUseCase
{
	use UseCaseTrait;

	/**
	 * @param AddSeasonTeamMemberRequest $request
	 * @return AddSeasonTeamMemberResponse
	 * @throws DomainException
	 */
	public function execute(AddSeasonTeamMemberRequest $request): AddSeasonTeamMemberResponse
	{
		try {
			$coach = $this->getContainer()->getPlayerRepository()->findById($request->getCoachId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('Coach not found with id ' . $request->getCoachId());
		}

		try {
			$seasonTeam = $this->getContainer()->getSeasonTeamRepository()->findById($request->getSeasonTeamId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('SeasonTeam not found with id ' . $request->getSeasonTeamId());
		}

		if ($seasonTeam->getCoach()->getId() !== $coach->getId()) {
			throw new DomainException(sprintf('Coach with id %d is not a SeasonTeam Coach', $coach->getId()));
		}

		try {
			$player = $this->getContainer()->getPlayerRepository()->findById($request->getPlayerId());
		} catch (EntityNotFoundException $e) {
			throw new DomainException('Player not found with id ' . $request->getPlayerId());
		}

		try {
			$member = $this->getContainer()->getSeasonTeamMemberRepository()->findByPlayerAndSeason($player, $seasonTeam->getSeason());
			if ($member->getSeasonTeam()->getId() === $seasonTeam->getId()) {
				throw new DomainException(sprintf('Player with id %d already in this SeasonTeam'));
			}
			throw new DomainException(sprintf('Player with id %d already in another SeasonTeam'));
		} catch (EntityNotFoundException $e) {}

		$member = SeasonTeamMember::create($seasonTeam, $player, $request->getType());
		$this->getContainer()->getSeasonTeamMemberRepository()->save($member);
		return new AddSeasonTeamMemberResponse($member);
	}
}