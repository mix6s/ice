<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 22:05
 */

namespace Domain\DTO\Request;

/**
 * Class CreateSeasonRequest
 * @package Domain\DTO\Request
 */
class CreateSeasonRequest
{
	private $year;

	/**
	 * CreateSeasonRequest constructor.
	 * @param int $year
	 */
	public function __construct(int $year)
	{
		$this->year = $year;
	}

	/**
	 * @return int
	 */
	public function getYear(): int
	{
		return $this->year;
	}
}