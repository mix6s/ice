<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 30.06.2017
 * Time: 11:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
 */
class User extends \FOS\UserBundle\Model\User
{
	const ROLE_PLAYER = 'ROLE_PLAYER';
	const ROLE_FAN = 'ROLE_FAN';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="PlayerProfile", mappedBy="user", cascade={"persist"})
	 */
	private $profile;

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
	 * @return PlayerProfile|null
	 * @throws \RuntimeException
	 */
	public function getPlayerProfile()
	{
		if ($this->hasRole(self::ROLE_PLAYER)) {
			return $this->profile;
		}
		throw new \RuntimeException();
	}

	/**
	 * @param PlayerProfile $profile
	 */
	public function setPlayerProfile(PlayerProfile $profile)
	{
		if (!$this->hasRole(self::ROLE_PLAYER)) {
			throw new \RuntimeException();
		}
		if ($profile->getUser()->getId() != $this->getId()) {
			throw new \RuntimeException();
		}
		$this->profile = $profile;
	}

	/**
	 * @return bool
	 */
	public function isPlayer()
	{
		return $this->hasRole(self::ROLE_PLAYER);
	}
}