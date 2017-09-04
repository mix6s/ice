<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 22:04
 */

namespace Domain\UseCase;


use Domain\DTO\Request\AddSeasonTeamMemberRequest;
use Domain\DTO\Request\CopySeasonRequest;
use Domain\DTO\Request\CreateSeasonRequest;
use Domain\DTO\Request\CreateSeasonTeamRequest;
use Domain\DTO\Response\CopySeasonResponse;
use Domain\Entity\Season;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exception\SeasonAlreadyExistException;

/**
 * Class CopySeasonUseCase
 * @package Domain\UseCase
 */
class CopySeasonUseCase
{
	use UseCaseTrait;

	/**
	 * @param CopySeasonRequest $request
	 * @return CopySeasonResponse
	 * @throws SeasonAlreadyExistException
	 */
	public function execute(CopySeasonRequest $request): CopySeasonResponse
	{
		$season = (new CreateSeasonUseCase($this->getContainer()))
			->execute(new CreateSeasonRequest($request->getYear()))
			->getSeason();

		$seasonToCopy = $this->getContainer()->getSeasonRepository()->findById($request->getCopySeasonId());

		$seasonTeams = $this->getContainer()->getSeasonTeamRepository()->findBySeason($seasonToCopy);

		$createUseCase = new CreateSeasonTeamUseCase($this->getContainer());
		$addMemberUseCase = new AddSeasonTeamMembersUseCase($this->getContainer());

		$newSeasonTeams = [];
		foreach ($seasonTeams as $seasonTeam) {
			$newSeasonTeam = $createUseCase->execute(
				new CreateSeasonTeamRequest(
					$seasonTeam->getTeam()->getId(),
					$seasonTeam->getCoach()->getId(),
					$season->getId(),
					$seasonTeam->getLeague()->getId()
				)
			)->getSeasonTeam();
			$membersToCopy = $this->getContainer()->getSeasonTeamMemberRepository()->findBySeasonTeam($seasonTeam);
			$addRequest = new AddSeasonTeamMemberRequest($newSeasonTeam->getCoach()->getId(), $newSeasonTeam->getId());
			foreach ($membersToCopy as $member) {
				$addRequest->addMember($member->getPlayer()->getId(), $member->getType());
			}
			$addMemberUseCase->execute($addRequest);
			$newSeasonTeams[] = $newSeasonTeam;
		}
		return new CopySeasonResponse($season, $newSeasonTeams);
	}
}