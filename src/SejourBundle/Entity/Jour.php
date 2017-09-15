<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jour
 *
 * @ORM\Table(name="jour")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\JourRepository")
 */
class Jour
{
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
     * @var \DateTime
     *
     * @ORM\Column(name="Date", type="date")
     */
    private $date;

	/**
	* @ORM\Column(name="nb_act_matin", type="integer")
	*/
	private $nbActMatin = 0;
		
	/**
	* @ORM\Column(name="nb_act_am", type="integer")
	*/
	private $nbActAM = 0;
		
	/**
	* @ORM\Column(name="nb_act_soir", type="integer")
	*/
	private $nbActSoir = 0;
	
    /**
     * @var bool
     *
     * @ORM\Column(name="soinvalide", type="boolean")
     */
    private $SoinValide=false;
	
	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	 * @ORM\JoinColumn(nullable=true)
	*/
	private $SoinValidePar=null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SoinValideDate", type="datetime", nullable=true)
     */
    private $SoinValideDate=null;	

	public function increaseMatin()
	{
		$this->nbActMatin++;
	}

	public function decreaseMatin()
	{
		$this->nbActMatin--;
	}

	public function increaseAM()
	{
		$this->nbActAM++;
	}

	public function decreaseAM()
	{
		$this->nbActAM--;
	}

	public function increaseSoir()
	{
		$this->nbActSoir++;
	}

	public function decreaseSoir()
	{
		$this->nbActSoir--;
	}
	
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Jour
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
     * Set sejour
     *
     * @param \SejourBundle\Entity\Sejour $sejour
     *
     * @return Jour
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
     * Set soinValide
     *
     * @param boolean $soinValide
     *
     * @return Jour
     */
    public function setSoinValide($soinValide)
    {
        $this->SoinValide = $soinValide;

        return $this;
    }

    /**
     * Get soinValide
     *
     * @return boolean
     */
    public function getSoinValide()
    {
        return $this->SoinValide;
    }

    /**
     * Set soinValideDate
     *
     * @param \DateTime $soinValideDate
     *
     * @return Jour
     */
    public function setSoinValideDate($soinValideDate)
    {
        $this->SoinValideDate = $soinValideDate;

        return $this;
    }

    /**
     * Get soinValideDate
     *
     * @return \DateTime
     */
    public function getSoinValideDate()
    {
        return $this->SoinValideDate;
    }

    /**
     * Set soinValidePar
     *
     * @param \UserBundle\Entity\User $soinValidePar
     *
     * @return Jour
     */
    public function setSoinValidePar(\UserBundle\Entity\User $soinValidePar)
    {
        $this->SoinValidePar = $soinValidePar;

        return $this;
    }

    /**
     * Get soinValidePar
     *
     * @return \UserBundle\Entity\User
     */
    public function getSoinValidePar()
    {
        return $this->SoinValidePar;
    }
	
	public function getDateFormulaire()
	{
		return $this->getDate()->format('d/m');
	}

    /**
     * Set nbActMatin
     *
     * @param integer $nbActMatin
     *
     * @return Jour
     */
    public function setNbActMatin($nbActMatin)
    {
        $this->nbActMatin = $nbActMatin;

        return $this;
    }

    /**
     * Get nbActMatin
     *
     * @return integer
     */
    public function getNbActMatin()
    {
        return $this->nbActMatin;
    }

    /**
     * Set nbActAM
     *
     * @param integer $nbActAM
     *
     * @return Jour
     */
    public function setNbActAM($nbActAM)
    {
        $this->nbActAM = $nbActAM;

        return $this;
    }

    /**
     * Get nbActAM
     *
     * @return integer
     */
    public function getNbActAM()
    {
        return $this->nbActAM;
    }

    /**
     * Set nbActSoir
     *
     * @param integer $nbActSoir
     *
     * @return Jour
     */
    public function setNbActSoir($nbActSoir)
    {
        $this->nbActSoir = $nbActSoir;

        return $this;
    }

    /**
     * Get nbActSoir
     *
     * @return integer
     */
    public function getNbActSoir()
    {
        return $this->nbActSoir;
    }
}
