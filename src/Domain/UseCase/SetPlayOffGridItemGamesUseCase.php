<?php

namespace Domain\UseCase;


use Domain\DTO\Request\SetPlayOffGridItemGamesRequest;
use Domain\DTO\Response\SetPlayOffGridItemGamesResponse;

class SetPlayOffGridItemGamesUseCase
{
	use UseCaseTrait;

	public function execute(SetPlayOffGridItemGamesRequest $request): SetPlayOffGridItemGamesResponse
	{
		return new SetPlayOffGridItemGamesResponse();
	}
}