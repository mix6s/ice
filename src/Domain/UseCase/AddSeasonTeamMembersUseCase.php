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
 * Class AddSeasonTeamMembersUseCase
 * @package Domain\UseCase
 */
class AddSeasonTeamMembersUseCase
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

		$members = [];
		foreach ($request->getMembers() as $memberDTO) {
			try {
				$player = $this->getContainer()->getPlayerRepository()->findById($memberDTO->getPlayerId());
			} catch (EntityNotFoundException $e) {
				throw new DomainException('Player not found with id ' . $memberDTO->getPlayerId());
			}

			try {
				if (isset($members[$player->getId()])) {
					throw new DomainException(sprintf('Player with id %d already in this SeasonTeam', $player->getId()));
				}
				$member = $this->getContainer()->getSeasonTeamMemberRepository()->findByPlayerAndSeason($player, $seasonTeam->getSeason());
				if ($member->getSeasonTeam()->getId() === $seasonTeam->getId()) {
					throw new DomainException(sprintf('Player with id %d already in this SeasonTeam', $player->getId()));
				}
				throw new DomainException(sprintf('Player with id %d already in another SeasonTeam', $player->getId()));
			} catch (EntityNotFoundException $e) {
				$memberId = $this->getContainer()->getSeasonTeamMemberRepository()->getNextId();
				$member = SeasonTeamMember::create($memberId, $seasonTeam, $player, $memberDTO->getType());
				$this->getContainer()->getSeasonTeamMemberRepository()->save($member);
				$members[$member->getPlayer()->getId()] = $member;
			}
		}

		return new AddSeasonTeamMemberResponse($members);
	}
}