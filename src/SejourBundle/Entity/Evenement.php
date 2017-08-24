<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\EvenementRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Evenement
{
	/**
	* @ORM\ManyToMany(targetEntity="SejourBundle\Entity\Enfant", cascade={"persist"}, inversedBy="evenements")
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
	 * @ORM\ManyToOne(
	 *      targetEntity="SejourBundle\Entity\Activite",
	 *      inversedBy="evenement"
	 * )
	 * @ORM\JoinColumn(onDelete="CASCADE")
	*/
	protected $activite;
	
	/**
     * @var int
     *
     * @ORM\Column(name="Moment", type="integer")
	 * @Assert\Range(
     *      min = 1,
     *      max = 5,
     * )
     */
    private $Moment;
	
	/**
     * @var int
     *
     * @ORM\Column(name="NbPlaces", type="integer")
	 *
     */
    private $NbPlaces;
	
	/**
     * @var int
     *
     * @ORM\Column(name="NbInscrits", type="integer")
	 *
     */
    private $NbInscrits=0;
	
	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Jour")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $jour;
	
	 /**
     * @var bool
     *
     * @ORM\Column(name="estlie", type="boolean")
     */
    private $estlie=false;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set moment
     *
     * @param integer $moment
     *
     * @return Evenement
     */
    public function setMoment($moment)
    {
        $this->Moment = $moment;

        return $this;
    }

    /**
     * Get moment
     *
     * @return integer
     */
    public function getMoment()
    {
        return $this->Moment;
    }

    /**
     * Set activite
     *
     * @param \SejourBundle\Entity\Activite $activite
     *
     * @return Evenement
     */
    public function setActivite(\SejourBundle\Entity\Activite $activite = null)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return \SejourBundle\Entity\Activite
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set jour
     *
     * @param \SejourBundle\Entity\Jour $jour
     *
     * @return Evenement
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
     * Constructor
     */
    public function __construct()
    {
        $this->enfant = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add enfant
     *
     * @param \SejourBundle\Entity\Enfant $enfant
     *
     * @return Evenement
     */
    public function addEnfant(\SejourBundle\Entity\Enfant $enfant)
    {
        $this->enfant[] = $enfant;
		$this->NbInscrits = $this->getNbInscrits() +1;
        return $this;
    }

    /**
     * Remove enfant
     *
     * @param \SejourBundle\Entity\Enfant $enfant
     */
    public function removeEnfant(\SejourBundle\Entity\Enfant $enfant)
    {
        $this->enfant->removeElement($enfant);
		$this->NbInscrits = $this->getNbInscrits() -1;
    }

    /**
     * Get enfant
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnfant()
    {
        return $this->enfant;
    }
	
	/**
	* @ORM\PrePersist
	*/
	public function increase()
	{
		$Moment=$this->getMoment();
		if($Moment == 1)
		{
			$this->getJour()->increaseM1();
		}
		elseif($Moment == 2)
		{
			$this->getJour()->increaseM2();
		}
		elseif($Moment == 3)
		{
			$this->getJour()->increaseAM();
		}
		elseif($Moment == 4)
		{
			$this->getJour()->increaseJourMatin();
		}
		elseif($Moment == 5)
		{
			$this->getJour()->increaseJour();
		}
	}
	
	/**
	* @ORM\PreRemove
	*/
	public function decrease()
	{
		$Moment=$this->getMoment();
		if($Moment == 1)
		{
			$this->getJour()->decreaseM1();
		}
		elseif($Moment == 2)
		{
			$this->getJour()->decreaseM2();
		}
		elseif($Moment == 3)
		{
			$this->getJour()->decreaseAM();
		}
		elseif($Moment == 4)
		{
			$this->getJour()->decreaseJourMatin();
		}
		elseif($Moment == 5)
		{
			$this->getJour()->decreaseJour();
		}
	}

    /**
     * Set nbPlaces
     *
     * @param integer $nbPlaces
     *
     * @return Evenement
     */
    public function setNbPlaces($nbPlaces)
    {
        $this->NbPlaces = $nbPlaces;

        return $this;
    }

    /**
     * Get nbPlaces
     *
     * @return integer
     */
    public function getNbPlaces()
    {
        return $this->NbPlaces;
    }

    /**
     * Set nbInscrits
     *
     * @param integer $nbInscrits
     *
     * @return Evenement
     */
    public function setNbInscrits($nbInscrits)
    {
        $this->NbInscrits = $nbInscrits;

        return $this;
    }

    /**
     * Get nbInscrits
     *
     * @return integer
     */
    public function getNbInscrits()
    {
        return $this->NbInscrits;
    }

    /**
     * Set estlie
     *
     * @param boolean $estlie
     *
     * @return Evenement
     */
    public function setEstlie($estlie)
    {
        $this->estlie = $estlie;

        return $this;
    }

    /**
     * Get estlie
     *
     * @return boolean
     */
    public function getEstlie()
    {
        return $this->estlie;
    }
}
