<?php

namespace Domain\UseCase;


use Domain\DTO\Request\SetPlayOffGridItemSeasonTeamsRequest;
use Domain\DTO\Response\SetPlayOffGridItemSeasonTeamsResponse;

class SetPlayOffGridItemSeasonTeamsUseCase
{
	use UseCaseTrait;

	public function execute(SetPlayOffGridItemSeasonTeamsRequest $request): SetPlayOffGridItemSeasonTeamsResponse
	{
		return new SetPlayOffGridItemSeasonTeamsResponse();
	}
}