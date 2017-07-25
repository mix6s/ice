<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:20
 */

namespace Domain\Entity;

/**
 * Class Season
 * @package Domain\Entity
 */
class Season
{
	private $id;
	private $year;

	/**
	 * Season constructor.
	 * @param int $year
	 */
	private function __construct(int $year)
	{
		$this->year = $year;
	}

	/**
	 * @param int $year
	 * @return Season
	 */
	public static function create(int $year): Season
	{
		return new Season($year);
	}


	/**
	 * @return int
	 */
	public function getYear(): int
	{
		return $this->year;
	}
}