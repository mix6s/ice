<?php

namespace Domain\UseCase;


use Domain\DTO\Request\UpdatePlayOffGridItemGameRequest;
use Domain\DTO\Response\UpdatePlayOffGridItemGameResponse;

class UpdatePlayOffGridItemGameUseCase
{
	use UseCaseTrait;

	public function execute(UpdatePlayOffGridItemGameRequest $request): UpdatePlayOffGridItemGameResponse
	{
		return new UpdatePlayOffGridItemGameResponse();
	}
}