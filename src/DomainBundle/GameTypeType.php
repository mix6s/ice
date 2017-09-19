<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 18.09.2017
 * Time: 21:56
 */

namespace DomainBundle;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Domain\ValueObject\GameType;

/**
 * Class GameTypeType
 * @package DomainBundle
 */
class GameTypeType extends Type
{
	const MYTYPE = 'gametype'; // modify to match your type name

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return GameType::resolve($value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return (string)$value;
	}

	public function getName()
	{
		return self::MYTYPE; // modify to match your constant name
	}
}