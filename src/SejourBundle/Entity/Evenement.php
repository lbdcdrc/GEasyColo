<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @var time
     *
     * @ORM\Column(name="heureDebut", type="time")
	 *
     */
    private $heureDebut;
	
	/**
     * @var time
     *
     * @ORM\Column(name="heureFin", type="time")
	 *
     */
    private $heureFin;
	
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
		$HeureDebut=$this->getheureDebut();
		$HeureFin=$this->getheureFin();
		$HeureDebutMatin = new \DateTime('1970-01-01 07:00');
		$HeureFinMatin = new \DateTime('1970-01-01 13:00');
		$HeureDebutAM = new \DateTime('1970-01-01 12:00');
		$HeureFinAM = new \DateTime('1970-01-01 19:00');
		$HeureDebutSoir = new \DateTime('1970-01-01 19:00');
		if($HeureDebut >= $HeureDebutMatin and $HeureFin <= $HeureFinMatin )
		{
			$this->getJour()->increaseMatin();
		}
		elseif($HeureDebut >= $HeureDebutAM and $HeureFin <= $HeureFinAM )
		{
			$this->getJour()->increaseAM();
		}
		elseif($HeureDebut >= $HeureDebutSoir)
		{
			$this->getJour()->increaseSoir();
		}
		elseif($HeureDebut >= $HeureDebutMatin and $HeureFin <= $HeureFinAM)
		{
			$this->getJour()->increaseMatin();
			$this->getJour()->increaseAM();
		}
		else
		{
			$this->getJour()->increaseMatin();
			$this->getJour()->increaseAM();
			$this->getJour()->increaseSoir();
		}
	}
	
	/**
	* @ORM\PreRemove
	*/
	public function decrease()
	{
		$HeureDebut=$this->getheureDebut();
		$HeureFin=$this->getheureFin();
		$HeureDebutMatin = new \DateTime('1970-01-01 07:00');
		$HeureFinMatin = new \DateTime('1970-01-01 13:00');
		$HeureDebutAM = new \DateTime('1970-01-01 12:00');
		$HeureFinAM = new \DateTime('1970-01-01 19:00');
		$HeureDebutSoir = new \DateTime('1970-01-01 19:00');
		if($HeureDebut >= $HeureDebutMatin and $HeureFin <= $HeureFinMatin )
		{
			$this->getJour()->decreaseMatin();
		}
		elseif($HeureDebut >= $HeureDebutAM and $HeureFin <= $HeureFinAM )
		{
			$this->getJour()->decreaseAM();
		}
		elseif($HeureDebut >= $HeureDebutSoir)
		{
			$this->getJour()->decreaseSoir();
		}
		elseif($HeureDebut >= $HeureDebutMatin and $HeureFin <= $HeureFinAM)
		{
			$this->getJour()->decreaseMatin();
			$this->getJour()->decreaseAM();
		}
		else
		{
			$this->getJour()->decreaseMatin();
			$this->getJour()->decreaseAM();
			$this->getJour()->decreaseSoir();
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

    /**
     * Set heureDebut
     *
     * @param \DateTime $heureDebut
     *
     * @return Evenement
     */
    public function setHeureDebut($heureDebut)
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    /**
     * Get heureDebut
     *
     * @return \DateTime
     */
    public function getHeureDebut()
    {
        return $this->heureDebut;
    }

    /**
     * Set heureFin
     *
     * @param \DateTime $heureFin
     *
     * @return Evenement
     */
    public function setHeureFin($heureFin)
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    /**
     * Get heureFin
     *
     * @return \DateTime
     */
    public function getHeureFin()
    {
        return $this->heureFin;
    }
	/**
	 * @Assert\Callback
	*/
	public function isHeureValid(ExecutionContextInterface $context)
	{
		if ($this->getHeureDebut() >= $this->getHeureFin()) {
		  // La règle est violée, on définit l'erreur
		  $context
			->buildViolation('L\'heure de début ne peut être supérieure à l\'heure de fin !') // message
			->atPath('heureDebut')                                                   // attribut de l'objet qui est violé
			->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
		  ;
		}
	}	

}
