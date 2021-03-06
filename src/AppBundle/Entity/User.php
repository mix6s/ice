<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 30.06.2017
 * Time: 11:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\Entity\Player;
use DomainBundle\Entity\PlayerMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @UniqueEntity(
 *     fields={"usernameCanonical", "emailCanonical"},
 *     errorPath="email",
 *     message="fos_user.email.already_used",
 * 	   groups={"AppRegistration"}
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class User extends \FOS\UserBundle\Model\User
{
	const ROLE_ADMIN = 'ROLE_ADMIN';
	const ROLE_CONTROL = 'ROLE_CONTROL';
	const ROLE_COACH = 'ROLE_COACH';
	const ROLE_PLAYER = 'ROLE_PLAYER';
	const ROLE_FAN = 'ROLE_FAN';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="Domain\Entity\Player", cascade={"persist"})
	 * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
	 */
	private $player;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param $role
	 * @return bool
	 */
	public function isGranted($role)
	{
		return in_array($role, $this->getRoles());
	}

	/**
	 * @param string $username
	 * @return \FOS\UserBundle\Model\UserInterface
	 */
	public function setEmail($username)
	{
		$this->setUsername($username);

		return parent::setEmail($username);
	}

	/**
	 * @param string $usernameCanonical
	 * @return \FOS\UserBundle\Model\UserInterface
	 */
	public function setEmailCanonical($usernameCanonical)
	{
		$this->setUsernameCanonical($usernameCanonical);

		return parent::setEmailCanonical($usernameCanonical);
	}

	/**
	 * @param $role
	 */
	public function setRegRole($role)
	{
		switch ($role) {
			case self::ROLE_PLAYER:
			case self::ROLE_FAN:
				$this->setRoles([$role]);
				break;
			default:
				break;
		}
	}

	/**
	 * @return null|string
	 */
	public function getRegRole()
	{
		if ($this->hasRole(self::ROLE_FAN)) {
			return self::ROLE_FAN;
		} elseif ($this->hasRole(self::ROLE_PLAYER)) {
			return self::ROLE_PLAYER;
		}
		return null;
	}

	/**
	 * @return PlayerMetadata|null
	 * @throws \RuntimeException
	 */
	public function getPlayerProfile()
	{
		if ($this->hasRole(self::ROLE_PLAYER)) {
			return $this->getPlayer()->getMetadata();
		}
		throw new \RuntimeException();
	}

	/**
	 * @param PlayerMetadata $profile
	 */
	public function setPlayerProfile(PlayerMetadata $profile)
	{
		if (!$this->hasRole(self::ROLE_PLAYER)) {
			throw new \RuntimeException();
		}
		$this->getPlayer()->setMetadata($profile);
	}

	/**
	 * @return bool
	 */
	public function isPlayer()
	{
		return $this->hasRole(self::ROLE_PLAYER);
	}

	/**
	 * @return Player|null
	 */
	public function getPlayer()
	{
		return $this->player;
	}

	/**
	 * @param int $id
	 * @param $metadata
	 */
	public function createPlayer(int $id, $metadata)
	{
		$this->player = Player::create($id, $metadata);
	}
}