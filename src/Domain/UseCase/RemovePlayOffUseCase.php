<?php

namespace Domain\UseCase;


use Domain\DTO\Request\RemovePlayOffRequest;
use Domain\DTO\Response\RemovePlayOffResponse;

class RemovePlayOffUseCase
{
	use UseCaseTrait;

	public function execute(RemovePlayOffRequest $request): RemovePlayOffResponse
	{
		$playOff = $this->getContainer()->getPlayOffRepository()->findById($request->getId());
		$this->getContainer()->getPlayOffRepository()->remove($playOff);
		return new RemovePlayOffResponse();
	}
}