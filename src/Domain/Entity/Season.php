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
	 * @param int $id
	 * @param int $year
	 */
	private function __construct(int $id, int $year)
	{
		$this->id = $id;
		$this->year = $year;
	}

	/**
	 * @param int $id
	 * @param int $year
	 * @return Season
	 */
	public static function create(int $id, int $year): Season
	{
		return new Season($id, $year);
	}


	/**
	 * @return int
	 */
	public function getYear(): int
	{
		return $this->year;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}
}