<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 22:04
 */

namespace Domain\UseCase;


use Domain\DTO\Request\CreateSeasonRequest;
use Domain\DTO\Response\CreateSeasonResponse;
use Domain\Entity\Season;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exception\SeasonAlreadyExistException;

/**
 * Class CreateSeasonUseCase
 * @package Domain\UseCase
 */
class CreateSeasonUseCase
{
	use UseCaseTrait;

	/**
	 * @param CreateSeasonRequest $request
	 * @return CreateSeasonResponse
	 * @throws SeasonAlreadyExistException
	 */
	public function execute(CreateSeasonRequest $request): CreateSeasonResponse
	{
		$repository = $this->getContainer()->getSeasonRepository();
		try {
			$season = $repository->findByYear($request->getYear());
			throw new SeasonAlreadyExistException(sprintf('Season for %d year already exist', $season->getYear()));
		} catch (EntityNotFoundException $e) {}

		$season = Season::create($repository->getNextId(), $request->getYear());
		$repository->save($season);
		return new CreateSeasonResponse($season);
	}
}