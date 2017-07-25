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
	 * @throws DomainException
	 */
	public function execute(CreateSeasonRequest $request): CreateSeasonResponse
	{
		try {
			$season = $this->getContainer()->getSeasonRepository()->findByYear($request->getYear());
			throw new DomainException(sprintf('Season for %d year already exist', $season->getYear()));
		} catch (EntityNotFoundException $e) {}

		$season = Season::create($request->getYear());
		$this->getContainer()->getSeasonRepository()->save($season);
		return new CreateSeasonResponse($season);
	}
}