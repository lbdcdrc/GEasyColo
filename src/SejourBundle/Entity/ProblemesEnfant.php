<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProblemesEnfant
 *
 * @ORM\Table(name="problemes_enfant")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\ProblemesEnfantRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ProblemesEnfant
{
	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Enfant")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $enfant;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="probleme", type="text")
     */
    private $probleme;

    /**
     * @var bool
     *
     * @ORM\Column(name="encours", type="boolean")
     */
    private $encours;
	
	/**
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $ecrivain;
	
	/**
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=true)
	*/
    private $reglepar=null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datefin", type="datetime", nullable=true)
     */
    private $datefin;

	/**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->date  = new \DateTime("now");
		$this->getEnfant()->increaseProbleme();
    }



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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ProblemesEnfant
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set probleme
     *
     * @param string $probleme
     *
     * @return ProblemesEnfant
     */
    public function setProbleme($probleme)
    {
        $this->probleme = $probleme;

        return $this;
    }

    /**
     * Get probleme
     *
     * @return string
     */
    public function getProbleme()
    {
        return $this->probleme;
    }

    /**
     * Set encours
     *
     * @param boolean $encours
     *
     * @return ProblemesEnfant
     */
    public function setEncours($encours)
    {
        $this->encours = $encours;

        return $this;
    }

    /**
     * Get encours
     *
     * @return boolean
     */
    public function getEncours()
    {
        return $this->encours;
    }

    /**
     * Set datefin
     *
     * @param \DateTime $datefin
     *
     * @return ProblemesEnfant
     */
    public function setDatefin($datefin)
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * Get datefin
     *
     * @return \DateTime
     */
    public function getDatefin()
    {
        return $this->datefin;
    }

    /**
     * Set enfant
     *
     * @param \SejourBundle\Entity\Enfant $enfant
     *
     * @return ProblemesEnfant
     */
    public function setEnfant(\SejourBundle\Entity\Enfant $enfant)
    {
        $this->enfant = $enfant;

        return $this;
    }

    /**
     * Get enfant
     *
     * @return \SejourBundle\Entity\Enfant
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    /**
     * Set ecrivain
     *
     * @param \UserBundle\Entity\User $ecrivain
     *
     * @return ProblemesEnfant
     */
    public function setEcrivain(\UserBundle\Entity\User $ecrivain)
    {
        $this->ecrivain = $ecrivain;

        return $this;
    }

    /**
     * Get ecrivain
     *
     * @return \UserBundle\Entity\User
     */
    public function getEcrivain()
    {
        return $this->ecrivain;
    }

    /**
     * Set reglepar
     *
     * @param \UserBundle\Entity\User $reglepar
     *
     * @return ProblemesEnfant
     */
    public function setReglepar(\UserBundle\Entity\User $reglepar = null)
    {
        $this->reglepar = $reglepar;

        return $this;
    }

    /**
     * Get reglepar
     *
     * @return \UserBundle\Entity\User
     */
    public function getReglepar()
    {
        return $this->reglepar;
    }
}
