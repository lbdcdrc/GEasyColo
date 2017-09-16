<?php

namespace SejourBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Activite
 *
 * @ORM\Table(name="activite")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\ActiviteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Activite
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
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=255)
     */
    private $nom;
	
	 /**
     * @var string
     *
     * @ORM\Column(name="Description", type="text", nullable=true)
     */
    private $description;

	/**
     * @var string
     *
     * @ORM\Column(name="materiel", type="text", nullable=true)
     */
    private $materiel;
    
	/**
     * @var int
     *
     * @ORM\Column(name="nbAnim", type="integer")
     */
    private $nbAnim;
	/**
     * @var int
     *
     * @ORM\Column(name="nbEnfantMin", type="integer")
     */
    private $nbEnfantMin;
	/**
     * @var int
     *
     * @ORM\Column(name="nbEnfantMax", type="integer")
     */
    private $nbEnfantMax;
	/**
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $createur;

	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\CateActi")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $categorie;

	/**
	* @ORM\ManyToMany(targetEntity="SejourBundle\Entity\Sejour", inversedBy="activites", cascade={"persist"})
	*/
	private $sejour;	
	
	
	/**
	 * @ORM\OneToMany(targetEntity="SejourBundle\Entity\Evenement", mappedBy="activite")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $evenement;	
	

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Activite
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Activite
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->evenement = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add evenement
     *
     * @param \SejourBundle\Entity\Evenement $evenement
     *
     * @return Activite
     */
    public function addEvenement(\SejourBundle\Entity\Evenement $evenement)
    {
        $this->evenement[] = $evenement;

        return $this;
    }

    /**
     * Remove evenement
     *
     * @param \SejourBundle\Entity\Evenement $evenement
     */
    public function removeEvenement(\SejourBundle\Entity\Evenement $evenement)
    {
        $this->evenement->removeElement($evenement);
    }

    /**
     * Get evenement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvenement()
    {
        return $this->evenement;
    }

    /**
     * Set materiel
     *
     * @param string $materiel
     *
     * @return Activite
     */
    public function setMateriel($materiel)
    {
        $this->materiel = $materiel;

        return $this;
    }

    /**
     * Get materiel
     *
     * @return string
     */
    public function getMateriel()
    {
        return $this->materiel;
    }

    /**
     * Set nbAnim
     *
     * @param integer $nbAnim
     *
     * @return Activite
     */
    public function setNbAnim($nbAnim)
    {
        $this->nbAnim = $nbAnim;

        return $this;
    }

    /**
     * Get nbAnim
     *
     * @return integer
     */
    public function getNbAnim()
    {
        return $this->nbAnim;
    }

    /**
     * Set createur
     *
     * @param \UserBundle\Entity\User $createur
     *
     * @return Activite
     */
    public function setCreateur(\UserBundle\Entity\User $createur)
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * Get createur
     *
     * @return \UserBundle\Entity\User
     */
    public function getCreateur()
    {
        return $this->createur;
    }

    /**
     * Set categorie
     *
     * @param \SejourBundle\Entity\CateActi $categorie
     *
     * @return Activite
     */
    public function setCategorie(\SejourBundle\Entity\CateActi $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \SejourBundle\Entity\CateActi
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Add sejour
     *
     * @param \SejourBundle\Entity\Sejour $sejour
     *
     * @return Activite
     */
    public function addSejour(\SejourBundle\Entity\Sejour $sejour)
    {
        $this->sejour[] = $sejour;

        return $this;
    }

    /**
     * Remove sejour
     *
     * @param \SejourBundle\Entity\Sejour $sejour
     */
    public function removeSejour(\SejourBundle\Entity\Sejour $sejour)
    {
        $this->sejour->removeElement($sejour);
    }

    /**
     * Get sejour
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSejour()
    {
        return $this->sejour;
    }

    /**
     * Set nbEnfantMin
     *
     * @param integer $nbEnfantMin
     *
     * @return Activite
     */
    public function setNbEnfantMin($nbEnfantMin)
    {
        $this->nbEnfantMin = $nbEnfantMin;

        return $this;
    }

    /**
     * Get nbEnfantMin
     *
     * @return integer
     */
    public function getNbEnfantMin()
    {
        return $this->nbEnfantMin;
    }

    /**
     * Set nbEnfantMax
     *
     * @param integer $nbEnfantMax
     *
     * @return Activite
     */
    public function setNbEnfantMax($nbEnfantMax)
    {
        $this->nbEnfantMax = $nbEnfantMax;

        return $this;
    }

    /**
     * Get nbEnfantMax
     *
     * @return integer
     */
    public function getNbEnfantMax()
    {
        return $this->nbEnfantMax;
    }
	/**
	 * @ORM\PreUpdate
	*/
	public function checkNullPU()
	{
		if($this->materiel===null)
		{
			$this->materiel="";
		}
		if($this->description===null)
		{
			$this->description="";
		}
	}
	/**
	 * @ORM\PrePersist
	*/
	public function checkNullPP()
	{
		if($this->materiel===null)
		{
			$this->materiel="";
		}
		if($this->description===null)
		{
			$this->description="";
		}
	}
}
