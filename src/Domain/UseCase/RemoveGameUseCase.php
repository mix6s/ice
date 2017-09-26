<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 20:08
 */

namespace Domain\UseCase;


/**
 * Class RemoveGameUseCase
 * @package Domain\UseCase
 */
class RemoveGameUseCase
{
	use UseCaseTrait;

	/**
	 * @param int $gameId
	 */
	public function execute(int $gameId)
	{
		$game = $this->getContainer()->getGameRepository()->findById($gameId);
		$events = $this->getContainer()->getGameEventRepository()->findByGame($game);
		foreach ($events as $event) {
			$this->getContainer()->getGameEventRepository()->remove($event);
		}
		$this->getContainer()->getGameRepository()->remove($game);
	}
}