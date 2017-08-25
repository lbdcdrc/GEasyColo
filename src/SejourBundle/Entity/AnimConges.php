<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnimConges
 *
 * @ORM\Table(name="anim_conges")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\AnimCongesRepository")
 */
class AnimConges
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Jour")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $jour;
	
	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $user;	

	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\IdMoment")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $moment;


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
     * Set jour
     *
     * @param \SejourBundle\Entity\Jour $jour
     *
     * @return AnimConges
     */
    public function setJour(\SejourBundle\Entity\Jour $jour)
    {
        $this->jour = $jour;

        return $this;
    }

    /**
     * Get jour
     *
     * @return \SejourBundle\Entity\Jour
     */
    public function getJour()
    {
        return $this->jour;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return AnimConges
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

    /**
     * Set moment
     *
     * @param \SejourBundle\Entity\idMoment $moment
     *
     * @return AnimConges
     */
    public function setMoment(\SejourBundle\Entity\idMoment $moment)
    {
        $this->moment = $moment;

        return $this;
    }

    /**
     * Get moment
     *
     * @return \SejourBundle\Entity\idMoment
     */
    public function getMoment()
    {
        return $this->moment;
    }
}
