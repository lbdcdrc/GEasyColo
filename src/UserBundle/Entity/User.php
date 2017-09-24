<?php
// UserBundle/Entity/User.php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
	/**
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;
 	/**
	* @ORM\ManyToMany(targetEntity="SejourBundle\Entity\Evenement", mappedBy="animateurs")
	*/
	private $evenements;
  	
	/**
	* @ORM\OneToOne(targetEntity="SejourBundle\Entity\Image", cascade={"persist", "remove"})
	* @ORM\JoinColumn(nullable=true)
	*/
	private $image;	
  
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
	* @var string
	*
	* @ORM\Column(name="PN", type="string", length=255)
	*/
	private $PN;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="Telephone", type="string", length=255)
	 * @Assert\Regex(
     *     pattern="/^((\+)33\s?)[67](\s?\d{2}){4}$/",
     *     match=true,
     *     message="Le téléphone doit être au format international (+33601020304)"
     * )
	 */
	private $telephone;	
	
	/**
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\Diplome")
	* @ORM\JoinColumn(nullable=true)
	*/
	private $diplome;	
	
    /**
     * @var bool
     *
     * @ORM\Column(name="psc1", type="boolean")
     */
    private $psc1;
	
	 /**
     * @var bool
     *
     * @ORM\Column(name="sb", type="boolean")
     */
    private $sb;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="adresse1", type="string", length=255)
	 */
	private $adresse1;	

	/**
	 * @var string
	 *
	 * @ORM\Column(name="adresse2", type="string", length=255, nullable=true)
	 */
	private $adresse2=null;
	

	/**
	 * @var string
	 *
	 * @ORM\Column(name="codepostal", type="string", length=255)
	 * @Assert\Regex(
     *     pattern="/^(([0-8][0-9])|(9[0-5]))[0-9]{3}$/",
     *     match=true,
     *     message="Le code postal n'est pas valide."
	 * )
	 */
	private $codepostal;	

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ville", type="string", length=255)
	 */
	private $ville;		
	
	/**
	 * @var date
	 *
	 * @ORM\Column(name="naissance", type="date")
	 */
	private $naissance;	
	
	/**
	 * @var text
	 *
	 * @ORM\Column(name="presentation", type="text", nullable=true)
	 */
	private $presentation;	
	
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return User
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
     * @return User
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
     * Set telephone
     *
     * @param string $telephone
     *
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     *
     * @return User
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;

        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string
     */
    public function getAdresse1()
    {
        return $this->adresse1;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     *
     * @return User
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

	    public function getAge()
    {
        $dateInterval = $this->naissance->diff(new \DateTime());
 
        return $dateInterval->y;
    }
	    public function getPrenomNom()
    {
		$dateInterval = $this->naissance->diff(new \DateTime());
 
        $age = (int) $dateInterval->y;
		
		return $this->prenom.' '.$this->nom.' - '.$age.' ans - '.$this->email;
    }
    /**
     * Set codepostal
     *
     * @param string $codepostal
     *
     * @return User
     */
    public function setCodepostal($codepostal)
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    /**
     * Get codepostal
     *
     * @return string
     */
    public function getCodepostal()
    {
        return $this->codepostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return User
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set psc1
     *
     * @param boolean $psc1
     *
     * @return User
     */
    public function setPsc1($psc1)
    {
        $this->psc1 = $psc1;

        return $this;
    }

    /**
     * Get psc1
     *
     * @return boolean
     */
    public function getPsc1()
    {
        return $this->psc1;
    }

    /**
     * Set sb
     *
     * @param boolean $sb
     *
     * @return User
     */
    public function setSb($sb)
    {
        $this->sb = $sb;

        return $this;
    }

    /**
     * Get sb
     *
     * @return boolean
     */
    public function getSb()
    {
        return $this->sb;
    }

    /**
     * Set naissance
     *
     * @param \DateTime $naissance
     *
     * @return User
     */
    public function setNaissance($naissance)
    {
        $this->naissance = $naissance;

        return $this;
    }

    /**
     * Get naissance
     *
     * @return \DateTime
     */
    public function getNaissance()
    {
        return $this->naissance;
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     *
     * @return User
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set image
     *
     * @param \SejourBundle\Entity\Image $image
     *
     * @return User
     */
    public function setImage(\SejourBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \SejourBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
	
	/**
	* @ORM\PrePersist
	*/
	public function prenomnom()
	{
		$this->setPN($this->getPrenom().' '.$this->getNom());
	}

    /**
     * Set pN
     *
     * @param string $pN
     *
     * @return User
     */
    public function setPN($pN)
    {
        $this->PN = $pN;

        return $this;
    }

    /**
     * Get pN
     *
     * @return string
     */
    public function getPN()
    {
        return $this->PN;
    }

    /**
     * Set diplome
     *
     * @param \UserBundle\Entity\Diplome $diplome
     *
     * @return User
     */
    public function setDiplome(\UserBundle\Entity\Diplome $diplome = null)
    {
        $this->diplome = $diplome;

        return $this;
    }

    /**
     * Get diplome
     *
     * @return \UserBundle\Entity\Diplome
     */
    public function getDiplome()
    {
        return $this->diplome;
    }

    /**
     * Add evenement
     *
     * @param \SejourBundle\Entity\Evenement $evenement
     *
     * @return User
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
}
