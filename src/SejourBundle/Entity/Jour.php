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
	* @ORM\Column(name="nb_act_matin1", type="integer")
	*/
	private $nbActM1 = 0;
	
	/**
	* @ORM\Column(name="nb_act_matin2", type="integer")
	*/
	private $nbActM2 = 0;
	
	/**
	* @ORM\Column(name="nb_act_am", type="integer")
	*/
	private $nbActAM = 0;
	
	/**
	* @ORM\Column(name="nb_act_jour_matin", type="integer")
	*/
	private $nbActJourMatin = 0;
	
	/**
	* @ORM\Column(name="nb_act_journee", type="integer")
	*/
	private $nbActJour = 0;
	
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

	public function increaseM1()
	{
		$this->nbActM1++;
	}

	public function decreaseM1()
	{
		$this->nbActM1--;
	}
	
	public function increaseM2()
	{
		$this->nbActM2++;
	}

	public function decreaseM2()
	{
		$this->nbActM2--;
	}

	public function increaseAM()
	{
		$this->nbActAM++;
	}

	public function decreaseAM()
	{
		$this->nbActAM--;
	}

	public function increaseJourMatin()
	{
		$this->nbActJourMatin++;
	}

	public function decreaseJourMatin()
	{
		$this->nbActJourMatin--;
	}

	public function increaseJour()
	{
		$this->nbActJour++;
	}

	public function decreaseJour()
	{
		$this->nbActJour--;
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
     * Set nbActM1
     *
     * @param integer $nbActM1
     *
     * @return Jour
     */
    public function setNbActM1($nbActM1)
    {
        $this->nbActM1 = $nbActM1;

        return $this;
    }

    /**
     * Get nbActM1
     *
     * @return integer
     */
    public function getNbActM1()
    {
        return $this->nbActM1;
    }

    /**
     * Set nbActM2
     *
     * @param integer $nbActM2
     *
     * @return Jour
     */
    public function setNbActM2($nbActM2)
    {
        $this->nbActM2 = $nbActM2;

        return $this;
    }

    /**
     * Get nbActM2
     *
     * @return integer
     */
    public function getNbActM2()
    {
        return $this->nbActM2;
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

    /**
     * Set nbActJour
     *
     * @param integer $nbActJour
     *
     * @return Jour
     */
    public function setNbActJour($nbActJour)
    {
        $this->nbActJour = $nbActJour;

        return $this;
    }

    /**
     * Get nbActJour
     *
     * @return integer
     */
    public function getNbActJour()
    {
        return $this->nbActJour;
    }

    /**
     * Set nbActJourMatin
     *
     * @param integer $nbActJourMatin
     *
     * @return Jour
     */
    public function setNbActJourMatin($nbActJourMatin)
    {
        $this->nbActJourMatin = $nbActJourMatin;

        return $this;
    }

    /**
     * Get nbActJourMatin
     *
     * @return integer
     */
    public function getNbActJourMatin()
    {
        return $this->nbActJourMatin;
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
}
