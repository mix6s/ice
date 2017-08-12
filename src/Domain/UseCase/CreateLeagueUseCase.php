<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:26
 */

namespace Domain\UseCase;


use Domain\DTO\Request\CreateLeagueRequest;
use Domain\DTO\Response\CreateLeagueResponse;
use Domain\Entity\League;

/**
 * Class CreateLeagueUseCase
 * @package Domain\UseCase
 */
class CreateLeagueUseCase
{
	use UseCaseTrait;

	/**
	 * @param CreateLeagueRequest $request
	 * @return CreateLeagueResponse
	 */
	public function execute(CreateLeagueRequest $request): CreateLeagueResponse
	{
		$leagueRepository = $this->getContainer()->getLeagueRepository();
		$league = League::create($leagueRepository->getNextId(), $request->getMetadata());
		$leagueRepository->save($league);
		return new CreateLeagueResponse($league);
	}
}