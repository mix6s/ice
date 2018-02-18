<?php

namespace Domain\UseCase;


use Domain\DTO\Request\CreatePlayOffGridItemRequest;
use Domain\DTO\Response\CreatePlayOffGridItemResponse;

class CreatePlayOffGridItemUseCase
{
	use UseCaseTrait;

	public function execute(CreatePlayOffGridItemRequest $request): CreatePlayOffGridItemResponse
	{
		return new CreatePlayOffGridItemResponse();
	}
}