<?php


namespace Domain\UseCase;


use Domain\DTO\Request\UpdatePlayOffGridItemRequest;
use Domain\DTO\Response\UpdatePlayOffGridItemResponse;

class UpdatePlayOffGridItemUseCase
{
	use UseCaseTrait;

	public function execute(UpdatePlayOffGridItemRequest $request): UpdatePlayOffGridItemResponse
	{
		return new UpdatePlayOffGridItemResponse();
	}
}