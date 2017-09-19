<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SejourBundle\Entity\Jour;
use SejourBundle\Entity\Activite;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sejour
 *
 * @ORM\Table(name="sejour")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\SejourRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Sejour
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
     * @var \DateTime
     *
     * @ORM\Column(name="DateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateFin", type="date")
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="NomThema", type="string", length=255)
     */
    private $nomThema;
	/**
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $directeur;
	
	/**
	* @ORM\ManyToMany(targetEntity="SejourBundle\Entity\Activite", mappedBy="sejour")
	*/
    private $activites;
	
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
     * @var bool
     *
     * @ORM\Column(name="J15", type="boolean")
     */
    private $J15=true;
	
	/**
     * @var bool
     *
     * @ORM\Column(name="J3", type="boolean")
     */
    private $J3=true;
	
	/**
     * @var bool
     *
     * @ORM\Column(name="JProblemes", type="boolean")
     */
    private $JProblemes=true;
	
	/**
     * @var bool
     *
     * @ORM\Column(name="JTraitements", type="boolean")
     */
    private $JTraitements=true;

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Sejour
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Sejour
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set nomThema
     *
     * @param string $nomThema
     *
     * @return Sejour
     */
    public function setNomThema($nomThema)
    {
        $this->nomThema = $nomThema;

        return $this;
    }

    /**
     * Get nomThema
     *
     * @return string
     */
    public function getNomThema()
    {
        return $this->nomThema;
    }
	
	public function __toString()
    {
        return (string) $this->getId();
    }
	
	/**
	* @ORM\Column(name="nb_enfants", type="integer")
	*/
	private $nbEnfants = 0;

	public function increaseEnfants()
	{
		$this->nbEnfants++;
	}

	public function decreaseEnfants()
	{
		$this->nbEnfants--;
	}

    /**
     * Set nbEnfants
     *
     * @param integer $nbEnfants
     *
     * @return Sejour
     */
    public function setNbEnfants($nbEnfants)
    {
        $this->nbEnfants = $nbEnfants;

        return $this;
    }

    /**
     * Get nbEnfants
     *
     * @return integer
     */
    public function getNbEnfants()
    {
        return $this->nbEnfants;
    }
	


    /**
     * Set directeur
     *
     * @param \UserBundle\Entity\User $directeur
     *
     * @return Sejour
     */
    public function setDirecteur(\UserBundle\Entity\User $directeur)
    {
        $this->directeur = $directeur;

        return $this;
    }

    /**
     * Get directeur
     *
     * @return \UserBundle\Entity\User
     */
    public function getDirecteur()
    {
        return $this->directeur;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activites = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add activite
     *
     * @param \SejourBundle\Entity\Activite $activite
     *
     * @return Sejour
     */
    public function addActivite(\SejourBundle\Entity\Activite $activite)
    {
        $this->activites[] = $activite;

        return $this;
    }

    /**
     * Remove activite
     *
     * @param \SejourBundle\Entity\Activite $activite
     */
    public function removeActivite(\SejourBundle\Entity\Activite $activite)
    {
        $this->activites->removeElement($activite);
    }

    /**
     * Get activites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivites()
    {
        return $this->activites;
    }
	/**
	 * @Assert\Callback
	*/
	public function isDateValid(ExecutionContextInterface $context)
	{
		if ($this->getDateDebut() >= $this->getDateFin()) {
		  // La règle est violée, on définit l'erreur
		  $context
			->buildViolation('La date de début ne peut être supérieure à la date de fin !') // message
			->atPath('dateDebut')                                                   // attribut de l'objet qui est violé
			->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
		  ;
		}
	}		

    /**
     * Set j15
     *
     * @param boolean $j15
     *
     * @return Sejour
     */
    public function setJ15($j15)
    {
        $this->J15 = $j15;

        return $this;
    }

    /**
     * Get j15
     *
     * @return boolean
     */
    public function getJ15()
    {
        return $this->J15;
    }

    /**
     * Set j3
     *
     * @param boolean $j3
     *
     * @return Sejour
     */
    public function setJ3($j3)
    {
        $this->J3 = $j3;

        return $this;
    }

    /**
     * Get j3
     *
     * @return boolean
     */
    public function getJ3()
    {
        return $this->J3;
    }

    /**
     * Set jProblemes
     *
     * @param boolean $jProblemes
     *
     * @return Sejour
     */
    public function setJProblemes($jProblemes)
    {
        $this->JProblemes = $jProblemes;

        return $this;
    }

    /**
     * Get jProblemes
     *
     * @return boolean
     */
    public function getJProblemes()
    {
        return $this->JProblemes;
    }

    /**
     * Set jTraitements
     *
     * @param boolean $jTraitements
     *
     * @return Sejour
     */
    public function setJTraitements($jTraitements)
    {
        $this->JTraitements = $jTraitements;

        return $this;
    }

    /**
     * Get jTraitements
     *
     * @return boolean
     */
    public function getJTraitements()
    {
        return $this->JTraitements;
    }
}
