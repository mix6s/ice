<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 22.07.2017
 * Time: 13:52
 */

namespace DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class PlayerMetadata implements \JsonSerializable
{
	const POSITION_GK = 'goalkeeper';
	const POSITION_LB = 'leftback';
	const POSITION_RB = 'rightback';
	const POSITION_CF = 'centralforward';
	const POSITION_RF = 'rightforward';
	const POSITION_LF = 'leftforward';

	const STICK_R = 'right';
	const STICK_L = 'left';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;


	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 100,
	 *      minMessage = "Имя должно быть больше или равно {{ limit }} символов",
	 *      maxMessage = "Имя не должно превышать {{ limit }} символов"
	 * )
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 100,
	 *      minMessage = "Отчество должно быть больше или равно {{ limit }} символов",
	 *      maxMessage = "Отчество не должно превышать {{ limit }} символов"
	 * )
	 */
	private $secondName;

	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 100,
	 *      minMessage = "Фамилия должна быть больше или равна {{ limit }} символов",
	 *      maxMessage = "Фамилия не должна превышать {{ limit }} символов"
	 * )
	 */
	private $surname;

	/**
	 * @ORM\Column(type="datetime", length=100, nullable=true)
	 * @Assert\DateTime(
	 *     format = "d.m.Y",
	 *     message = "Некорректная дата рождения"
	 * )
	 */
	private $birthdate;

	/**
	 * @ORM\Column(type="string", length=20, nullable=true)
	 * @Assert\Choice({"goalkeeper", "leftback", "rightback", "centralforward", "leftforward", "rightforward"})
	 */
	private $position;

	/**
	 * @ORM\Column(type="string", length=20, nullable=true)
	 * @Assert\Choice({"left", "right"})
	 */
	private $stick;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $image;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @Assert\Type(
	 *     type="integer",
	 *     message="Значение должно быть целым числом"
	 * )
	 */
	private $height;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @Assert\Type(
	 *     type="integer",
	 *     message="Значение должно быть целым числом"
	 * )
	 */
	private $weight;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * @return mixed
	 */
	public function getSecondName()
	{
		return $this->secondName;
	}

	/**
	 * @return mixed
	 */
	public function getSurname()
	{
		return $this->surname;
	}

	/**
	 * @param mixed $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	/**
	 * @param mixed $secondName
	 */
	public function setSecondName($secondName)
	{
		$this->secondName = $secondName;
	}

	/**
	 * @param mixed $surname
	 */
	public function setSurname($surname)
	{
		$this->surname = $surname;
	}

	/**
	 * @return string
	 */
	public function getPlayerName()
	{
		$nameParts = [];
		if (!empty($this->surname)) {
			$nameParts[] = ucfirst($this->surname);
		}

		if (!empty($this->firstName)) {
			$nameParts[] = ucfirst($this->firstName);
		}
		return implode(' ', $nameParts);
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		$nameParts = [];
		if (!empty($this->surname)) {
			$nameParts[] = ucfirst($this->surname);
		}

		if (!empty($this->firstName)) {
			$nameParts[] = ucfirst($this->firstName);
		}

		if (!empty($this->secondName)) {
			$nameParts[] = ucfirst($this->secondName);
		}
		return implode(' ', $nameParts);
	}

	/**
	 * @return mixed
	 */
	public function getBirthdate()
	{
		return $this->birthdate;
	}

	/**
	 * @param mixed $birthdate
	 */
	public function setBirthdate($birthdate)
	{
		$this->birthdate = $birthdate;
	}

	/**
	 * @return mixed
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @param mixed $position
	 */
	public function setPosition($position)
	{
		$this->position = $position;
	}

	/**
	 * @return mixed
	 */
	public function getStick()
	{
		return $this->stick;
	}

	/**
	 * @param mixed $stick
	 */
	public function setStick($stick)
	{
		$this->stick = $stick;
	}

	/**
	 * @return mixed
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @param mixed $height
	 */
	public function setHeight($height)
	{
		$this->height = (int)$height;
	}

	/**
	 * @return mixed
	 */
	public function getWeight()
	{
		return $this->weight;
	}

	/**
	 * @param mixed $weight
	 */
	public function setWeight($weight)
	{
		$this->weight = (int)$weight;
	}

	/**
	 * @return mixed
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @param mixed $image
	 */
	public function setImage($image)
	{
		$this->image = $image;
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
			'surname' => $this->getSurname(),
			'first_name' => $this->getFirstName(),
			'second_name' => $this->getSecondName(),
			'full_name' => $this->getFullName(),
			'player_name' => $this->getPlayerName(),
			'position' => $this->getPosition()
		];
	}
}