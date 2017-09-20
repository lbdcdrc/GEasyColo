<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IdMoment
 *
 * @ORM\Table(name="id_moment")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\IdMomentRepository")
 */
class IdMoment
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
     * @ORM\Column(name="Moment", type="string", length=255)
     */
    private $moment;
    
	/**
     * @var string
     *
     * @ORM\Column(name="Couleur", type="string", length=255)
     */
    private $couleur;

	 /**
     * @var bool
     *
     * @ORM\Column(name="travail", type="boolean")
     */
    private $travail=false;	

	 /**
     * @var bool
     *
     * @ORM\Column(name="conges", type="boolean")
     */
    private $conges=false;	

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
     * @param string $moment
     *
     * @return idMoment
     */
    public function setMoment($moment)
    {
        $this->moment = $moment;

        return $this;
    }

    /**
     * Get moment
     *
     * @return string
     */
    public function getMoment()
    {
        return $this->moment;
    }

    /**
     * Set couleur
     *
     * @param string $couleur
     *
     * @return idMoment
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set travail
     *
     * @param boolean $travail
     *
     * @return IdMoment
     */
    public function setTravail($travail)
    {
        $this->travail = $travail;

        return $this;
    }

    /**
     * Get travail
     *
     * @return boolean
     */
    public function getTravail()
    {
        return $this->travail;
    }

    /**
     * Set heureDebut
     *
     * @param \DateTime $heureDebut
     *
     * @return IdMoment
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
     * @return IdMoment
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
     * Set conges
     *
     * @param boolean $conges
     *
     * @return IdMoment
     */
    public function setConges($conges)
    {
        $this->conges = $conges;

        return $this;
    }

    /**
     * Get conges
     *
     * @return boolean
     */
    public function getConges()
    {
        return $this->conges;
    }
}
