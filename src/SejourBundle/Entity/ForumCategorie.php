<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForumCategorie
 *
 * @ORM\Table(name="forum_categorie")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\ForumCategorieRepository")
 */
class ForumCategorie
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
     * @ORM\Column(name="Vues", type="integer")
     */
    private $vues=0;
	
    /**
     * @var string
     *
     * @ORM\Column(name="Reponses", type="integer")
     */
    private $reponses=0;
	
	/**
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $createur;

	/**
	* @ORM\OneToOne(targetEntity="SejourBundle\Entity\ForumMessage", cascade={"persist"})
	*/
	private $dernierMessage=null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Sejour")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $sejour;
	
	public function increaseVues()
	{
		$this->vues++;
	}
	public function increaseMessages()
	{
		$this->reponses++;
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
     * Set nom
     *
     * @param string $nom
     *
     * @return ForumCategorie
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
     * Set vues
     *
     * @param integer $vues
     *
     * @return ForumCategorie
     */
    public function setVues($vues)
    {
        $this->vues = $vues;

        return $this;
    }

    /**
     * Get vues
     *
     * @return integer
     */
    public function getVues()
    {
        return $this->vues;
    }

    /**
     * Set reponses
     *
     * @param integer $reponses
     *
     * @return ForumCategorie
     */
    public function setReponses($reponses)
    {
        $this->reponses = $reponses;

        return $this;
    }

    /**
     * Get reponses
     *
     * @return integer
     */
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * Set createur
     *
     * @param \UserBundle\Entity\User $createur
     *
     * @return ForumCategorie
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
     * Set dernierMessage
     *
     * @param \SejourBundle\Entity\ForumMessage $dernierMessage
     *
     * @return ForumCategorie
     */
    public function setDernierMessage(\SejourBundle\Entity\ForumMessage $dernierMessage = null)
    {
        $this->dernierMessage = $dernierMessage;

        return $this;
    }

    /**
     * Get dernierMessage
     *
     * @return \SejourBundle\Entity\ForumMessage
     */
    public function getDernierMessage()
    {
        return $this->dernierMessage;
    }

    /**
     * Set sejour
     *
     * @param \SejourBundle\Entity\Sejour $sejour
     *
     * @return ForumCategorie
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
}
