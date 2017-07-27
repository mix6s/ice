<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:33
 */

namespace DomainBundle\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="identity_team")
 */
class TeamIdentity implements IdentityInterface
{

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}
}