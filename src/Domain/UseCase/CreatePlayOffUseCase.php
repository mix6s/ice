<?php

namespace Domain\UseCase;


use Domain\DTO\Request\CreatePlayOffRequest;
use Domain\DTO\Response\CreatePlayOffResponse;

class CreatePlayOffUseCase
{
	use UseCaseTrait;

	public function execute(CreatePlayOffRequest $request): CreatePlayOffResponse
	{
		return new CreatePlayOffResponse();
	}
}