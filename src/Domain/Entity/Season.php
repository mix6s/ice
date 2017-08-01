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
class Season implements \JsonSerializable
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

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'year' => $this->getYear()
		];
	}
}