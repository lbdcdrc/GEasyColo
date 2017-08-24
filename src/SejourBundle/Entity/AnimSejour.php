<?php
// src/OC/PlatformBundle/Entity/AdvertSkill.php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="anim_sejour")
 */
class AnimSejour
{
	/**
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	private $id;

	/**
	* @ORM\Column(name="role", type="integer")
	* @Assert\Range(
	*      min = 1,
	*      max = 3,
	* )
	*/
	private $role;

	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Sejour")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $sejour;

	/**
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $user;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param integer $role
     *
     * @return AnimSejour
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set sejour
     *
     * @param \SejourBundle\Entity\Sejour $sejour
     *
     * @return AnimSejour
     */
    public function setSejour(\SejourBundle\Entity\Sejour $sejour)
    {
        $this->sejour = $sejour;

        return $this;
    }

    /**
     * Get sejour
     *
     * @return \SejourBundle\Entity\Sejour
     */
    public function getSejour()
    {
        return $this->sejour;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return AnimSejour
     */
    public function setUser(\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
