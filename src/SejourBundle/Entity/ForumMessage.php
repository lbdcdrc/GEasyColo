<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForumMessage
 *
 * @ORM\Table(name="forum_message")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\ForumMessageRepository")
 */
class ForumMessage
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
     * @ORM\Column(name="Message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateCreation", type="datetime")
     */
    private $dateCreation;

	/**
	 * @ORM\ManyToOne(targetEntity="SejourBundle\Entity\ForumCategorie")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $categorie;
	
	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $user;


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
     * Set message
     *
     * @param string $message
     *
     * @return ForumMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return ForumMessage
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set categorie
     *
     * @param \SejourBundle\Entity\ForumCategorie $categorie
     *
     * @return ForumMessage
     */
    public function setCategorie(\SejourBundle\Entity\ForumCategorie $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \SejourBundle\Entity\ForumCategorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return ForumMessage
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
}
