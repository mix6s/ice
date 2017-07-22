<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 30.06.2017
 * Time: 11:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppUserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends \FOS\UserBundle\Model\User
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @return mixed
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
}