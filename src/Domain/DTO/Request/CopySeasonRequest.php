<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 22:05
 */

namespace Domain\DTO\Request;

/**
 * Class CopySeasonRequest
 * @package Domain\DTO\Request
 */
class CopySeasonRequest
{
	private $year;
	private $copySeasonId;

	/**
	 * CopySeasonRequest constructor.
	 * @param int $copySeasonId
	 * @param int $year
	 */
	public function __construct(int $copySeasonId, int $year)
	{
		$this->year = $year;
		$this->copySeasonId = $copySeasonId;
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
	public function getCopySeasonId(): int
	{
		return $this->copySeasonId;
	}
}