<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 18.09.2017
 * Time: 19:30
 */

namespace Domain\ValueObject;


use Domain\Exception\DomainException;

/**
 * Class GameType
 * @package Domain\ValueObject
 */
final class GameType
{
	const TYPE_PLAYOFF = 'playoff';
	const TYPE_FINAL = 'final';
	const TYPE_REGULAR = 'regular';

	private $type;

	/**
	 * @return GameType
	 */
	public static function regular(): GameType
	{
		return new self(self::TYPE_REGULAR);
	}

	/**
	 * @return GameType
	 */
	public static function final(): GameType
	{
		return new self(self::TYPE_FINAL);
	}

	/**
	 * @return GameType
	 */
	public static function playoff(): GameType
	{
		return new self(self::TYPE_PLAYOFF);
	}

	/**
	 * GameType constructor.
	 * @param string $type
	 * @throws DomainException
	 */
	private function __construct(string $type)
	{
		if (!in_array($type, [self::TYPE_PLAYOFF, self::TYPE_FINAL, self::TYPE_REGULAR])) {
			throw new DomainException("Invalid game type");
		}
		$this->type = $type;
	}

	/**
	 * @param string $type
	 * @return GameType
	 */
	public static function resolve(string $type): GameType
	{
		return new self($type);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->type;
	}
}