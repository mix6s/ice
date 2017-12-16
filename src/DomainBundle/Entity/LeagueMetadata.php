<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:10
 */

namespace DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class LeagueMetadata implements \JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=200, nullable=true)
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 200,
	 *      minMessage = "Имя должно быть больше или равно {{ limit }} символов",
	 *      maxMessage = "Имя не должно превышать {{ limit }} символов"
	 * )
	 */
	private $title;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $playOffPlaces;

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @param $data
	 * @todo move to policy in another bundle
	 */
	public function updateFromData($data)
	{
		if (isset($data['title'])) {
			$this->setTitle($data['title']);
		}
		if (isset($data['play_off_places'])) {
			$this->setPlayOffPlaces($data['play_off_places']);
		}
	}

	/**
	 * @return mixed
	 */
	public function getId()
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
			'title' => $this->getTitle(),
			'play_off_places' => $this->getPlayOffPlaces()
		];
	}

	/**
	 * @return integer|null
	 */
	public function getPlayOffPlaces()
	{
		return $this->playOffPlaces;
	}

	/**
	 * @param integer $playOffPlaces
	 */
	public function setPlayOffPlaces($playOffPlaces)
	{
		$this->playOffPlaces = $playOffPlaces;
	}
}