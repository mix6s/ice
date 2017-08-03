<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 02.08.2017
 * Time: 18:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="setting")
 */
class Setting
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", unique=true)
	 */
	private $name;

	/**
	 * @ORM\Column(type="object")
	 */
	private $value;

	/**
	 * Setting constructor.
	 * @param string $name
	 */
	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
}