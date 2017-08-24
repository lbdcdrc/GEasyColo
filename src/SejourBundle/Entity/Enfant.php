<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enfant
 *
 * @ORM\Table(name="enfant")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\EnfantRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Enfant
{
	/**
	* @ORM\OneToOne(targetEntity="SejourBundle\Entity\Image", cascade={"persist", "remove"})
	* @ORM\JoinColumn(nullable=true)
	*/
	private $image;	
	
	/**
	* @ORM\ManyToMany(targetEntity="SejourBundle\Entity\Evenement", mappedBy="enfant")
	*/
	private $evenements;
	/**
	* @var string
	* 
	* @ORM\Column(name="Chambre", type="string", length=255, nullable=true)
	*
	*/
	private $chambre;

	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Sejour")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $sejour;
	
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
     * @ORM\Column(name="Prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var int
     *
     * @ORM\Column(name="Age", type="integer")
     */
    private $age;
	
	/**
     * @var int
     *
     * @ORM\Column(name="problemeencours", type="integer")
     */
    private $problemeencours = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="Infos", type="text", nullable=true)
     */
    private $infos;
    /**
     * @var string
     *
     * @ORM\Column(name="regimes", type="text", nullable=true)
     */
    private $regimes;

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
     * @return Enfant
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Enfant
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return Enfant
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set infos
     *
     * @param string $infos
     *
     * @return Enfant
     */
    public function setInfos($infos)
    {
        $this->infos = $infos;

        return $this;
    }

    /**
     * Get infos
     *
     * @return string
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * Set sejour
     *
     * @param \SejourBundle\Entity\Sejour $sejour
     *
     * @return Enfant
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
	* @ORM\PrePersist
	*/
	public function increase()
	{
		$this->getSejour()->increaseEnfants();
	}
	
	/**
	* @ORM\PreRemove
	*/
	public function decrease()
	{
		$this->getSejour()->decreaseEnfants();
	}

	public function increaseProbleme()
	{
		$this->problemeencours++;
	}

	public function decreaseProbleme()
	{
		$this->problemeencours--;
	}
	
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->evenements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add evenement
     *
     * @param \SejourBundle\Entity\Evenement $evenement
     *
     * @return Enfant
     */
    public function addEvenement(\SejourBundle\Entity\Evenement $evenement)
    {
        $this->evenements[] = $evenement;

        return $this;
    }

    /**
     * Remove evenement
     *
     * @param \SejourBundle\Entity\Evenement $evenement
     */
    public function removeEvenement(\SejourBundle\Entity\Evenement $evenement)
    {
        $this->evenements->removeElement($evenement);
    }

    /**
     * Get evenements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvenements()
    {
        return $this->evenements;
    }

    /**
     * Set problemeencours
     *
     * @param integer $problemeencours
     *
     * @return Enfant
     */
    public function setProblemeencours($problemeencours)
    {
        $this->problemeencours = $problemeencours;

        return $this;
    }

    /**
     * Get problemeencours
     *
     * @return integer
     */
    public function getProblemeencours()
    {
        return $this->problemeencours;
    }

	public function setImage(Image $image = null)
	{
	$this->image = $image;
	}
	public function getImage()
	{
	return $this->image;
	}

    /**
     * Set chambre
     *
     * @param string $chambre
     *
     * @return Enfant
     */
    public function setChambre($chambre)
    {
        $this->chambre = $chambre;

        return $this;
    }

    /**
     * Get chambre
     *
     * @return string
     */
    public function getChambre()
    {
        return $this->chambre;
    }

    /**
     * Set regimes
     *
     * @param string $regimes
     *
     * @return Enfant
     */
    public function setRegimes($regimes)
    {
        $this->regimes = $regimes;

        return $this;
    }

    /**
     * Get regimes
     *
     * @return string
     */
    public function getRegimes()
    {
        return $this->regimes;
    }
	
	public function getprenomnom()
	{
		return $this->getPrenom().' '.$this->getNom();
	}
}
