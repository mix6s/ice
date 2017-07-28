<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 22:40
 */

namespace Domain\Tests\UseCase;

use Domain\DTO\Request\CreateSeasonRequest;
use Domain\Exception\DomainException;
use Domain\Tests\UseCaseTestCase;
use Domain\UseCase\CreateSeasonUseCase;

/**
 * Class CreateSeasonUseCaseTest
 * @package Domain\Tests\UseCase
 */
class CreateSeasonUseCaseTest extends UseCaseTestCase
{
	/** @var  CreateSeasonUseCase */
	private $useCase;

	public function setUp()
	{
		parent::setUp();
		$this->useCase = new CreateSeasonUseCase($this->getContainer());
	}

	public function testShouldCreateSeason()
	{
		$response = $this->useCase->execute(new CreateSeasonRequest(2017));
		$storedSeason = $this->getContainer()->getSeasonRepository()->findById($response->getSeason()->getId());
		$this->assertEquals($storedSeason, $response->getSeason());
	}

	public function testShouldThrowExceptionOnAlreadyExistYearSeason()
	{
		$this->useCase->execute(new CreateSeasonRequest(2017));
		$this->expectException(DomainException::class);
		$this->useCase->execute(new CreateSeasonRequest(2017));
	}
}
