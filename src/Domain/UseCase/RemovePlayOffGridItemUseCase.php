<?php

namespace Domain\UseCase;


use Domain\DTO\Request\RemovePlayOffGridItemRequest;
use Domain\DTO\Response\RemovePlayOffGridItemResponse;

class RemovePlayOffGridItemUseCase
{
	use UseCaseTrait;

	public function execute(RemovePlayOffGridItemRequest $request): RemovePlayOffGridItemResponse
	{
		return new RemovePlayOffGridItemResponse();
	}
}