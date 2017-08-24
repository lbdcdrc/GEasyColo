<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForumUserMessageVu
 *
 * @ORM\Table(name="forum_user_message_vu")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\ForumUserMessageVuRepository")
 */
class ForumUserMessageVu
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
	* @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $user;
	
	/**
	 * @ORM\ManyToOne(targetEntity="SejourBundle\Entity\ForumCategorie")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $categorie;

	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\ForumMessage")
	*/
	private $dernierMessageVu=null;	
	
    /**
     * @var bool
     *
     * @ORM\Column(name="acceptenotification", type="boolean")
     */
    private $AccepteNotifications=true;
	
    /**
     * @var bool
     *
     * @ORM\Column(name="notifie", type="boolean")
     */
    private $Notifie=false;
	

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
     * Set accepteNotifications
     *
     * @param boolean $accepteNotifications
     *
     * @return ForumUserMessageVu
     */
    public function setAccepteNotifications($accepteNotifications)
    {
        $this->AccepteNotifications = $accepteNotifications;

        return $this;
    }

    /**
     * Get accepteNotifications
     *
     * @return boolean
     */
    public function getAccepteNotifications()
    {
        return $this->AccepteNotifications;
    }

    /**
     * Set notifie
     *
     * @param boolean $notifie
     *
     * @return ForumUserMessageVu
     */
    public function setNotifie($notifie)
    {
        $this->Notifie = $notifie;

        return $this;
    }

    /**
     * Get notifie
     *
     * @return boolean
     */
    public function getNotifie()
    {
        return $this->Notifie;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return ForumUserMessageVu
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

    /**
     * Set categorie
     *
     * @param \SejourBundle\Entity\ForumCategorie $categorie
     *
     * @return ForumUserMessageVu
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
     * Set dernierMessageVu
     *
     * @param \SejourBundle\Entity\ForumMessage $dernierMessageVu
     *
     * @return ForumUserMessageVu
     */
    public function setDernierMessageVu(\SejourBundle\Entity\ForumMessage $dernierMessageVu = null)
    {
        $this->dernierMessageVu = $dernierMessageVu;

        return $this;
    }

    /**
     * Get dernierMessageVu
     *
     * @return \SejourBundle\Entity\ForumMessage
     */
    public function getDernierMessageVu()
    {
        return $this->dernierMessageVu;
    }
}
