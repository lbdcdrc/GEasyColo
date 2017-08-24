<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Soin
 *
 * @ORM\Table(name="soin")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\SoinRepository")
 */
class Soin
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
	 * @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Enfant")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $enfant;	

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date", type="datetime")
     */
    private $date;

	/**
	 * @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Jour")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $jour;	
	
	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $User;	
	
    /**
     * @var \time
     *
     * @ORM\Column(name="Heure", type="time")
     */
    private $heure;
	
    /**
     * @var string
     *
     * @ORM\Column(name="Objet", type="text")
     */
    private $objet;

    /**
     * @var float
     *
     * @ORM\Column(name="Temperature", type="float", nullable=true)
     */
    private $temperature;

    /**
     * @var string
     *
     * @ORM\Column(name="SoinsDispenses", type="text")
     */
    private $soinsDispenses;

    /**
     * @var string
     *
     * @ORM\Column(name="Observations", type="text")
     */
    private $observations;


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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Soin
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set heure
     *
     * @param \DateTime $heure
     *
     * @return Soin
     */
    public function setHeure($heure)
    {
        $this->heure = $heure;

        return $this;
    }

    /**
     * Get heure
     *
     * @return \DateTime
     */
    public function getHeure()
    {
        return $this->heure;
    }

    /**
     * Set objet
     *
     * @param string $objet
     *
     * @return Soin
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set temperature
     *
     * @param float $temperature
     *
     * @return Soin
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get temperature
     *
     * @return float
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * Set soinsDispenses
     *
     * @param string $soinsDispenses
     *
     * @return Soin
     */
    public function setSoinsDispenses($soinsDispenses)
    {
        $this->soinsDispenses = $soinsDispenses;

        return $this;
    }

    /**
     * Get soinsDispenses
     *
     * @return string
     */
    public function getSoinsDispenses()
    {
        return $this->soinsDispenses;
    }

    /**
     * Set observations
     *
     * @param string $observations
     *
     * @return Soin
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * Get observations
     *
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * Set enfant
     *
     * @param \SejourBundle\Entity\Enfant $enfant
     *
     * @return Soin
     */
    public function setEnfant(\SejourBundle\Entity\Enfant $enfant)
    {
        $this->enfant = $enfant;

        return $this;
    }

    /**
     * Get enfant
     *
     * @return \SejourBundle\Entity\Enfant
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    /**
     * Set jour
     *
     * @param \SejourBundle\Entity\Jour $jour
     *
     * @return Soin
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
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Soin
     */
    public function setUser(\UserBundle\Entity\User $user)
    {
        $this->User = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->User;
    }
}
