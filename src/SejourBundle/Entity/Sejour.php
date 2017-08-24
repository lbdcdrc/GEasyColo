<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SejourBundle\Entity\Jour;
use SejourBundle\Entity\Activite;

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
}
