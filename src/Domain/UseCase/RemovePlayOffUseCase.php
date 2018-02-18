<?php

namespace Domain\UseCase;


use Domain\DTO\Request\RemovePlayOffRequest;
use Domain\DTO\Response\RemovePlayOffResponse;

class RemovePlayOffUseCase
{
	use UseCaseTrait;

	public function execute(RemovePlayOffRequest $request): RemovePlayOffResponse
	{
		return new RemovePlayOffResponse();
	}
}