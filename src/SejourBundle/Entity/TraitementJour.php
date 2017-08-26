<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TraitementJour
 *
 * @ORM\Table(name="traitement_jour")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\TraitementJourRepository")
 */
class TraitementJour
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
     * @var bool
     *
     * @ORM\Column(name="MatinCheck", type="boolean")
     */
    private $MatinCheck;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MatinDateCheck", type="datetime", nullable=true)
     */
    private $MatinDateCheck;
	
	 /**
     * @var bool
     *
     * @ORM\Column(name="MidiCheck", type="boolean")
     */
    private $MidiCheck;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MidiDateCheck", type="datetime", nullable=true)
     */
    private $MidiDateCheck;

	 /**
     * @var bool
     *
     * @ORM\Column(name="SoirCheck", type="boolean")
     */
    private $SoirCheck;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SoirDateCheck", type="datetime", nullable=true)
     */
    private $SoirDateCheck;	

	 /**
     * @var bool
     *
     * @ORM\Column(name="CoucheCheck", type="boolean")
     */
    private $CoucheCheck;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CoucheDateCheck", type="datetime", nullable=true)
     */
    private $CoucheDateCheck;	

	 /**
     * @var bool
     *
     * @ORM\Column(name="AutreCheck", type="boolean")
     */
    private $AutreCheck;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="AutreDateCheck", type="datetime", nullable=true)
     */
    private $AutreDateCheck;	

	/**
	 * @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Traitement")
	 * @ORM\JoinColumn(nullable=false)
	*/
	private $Traitement;

	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Jour")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $jour;
	
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
     * Set matinCheck
     *
     * @param boolean $matinCheck
     *
     * @return TraitementJour
     */
    public function setMatinCheck($matinCheck)
    {
        $this->MatinCheck = $matinCheck;

        return $this;
    }

    /**
     * Get matinCheck
     *
     * @return boolean
     */
    public function getMatinCheck()
    {
        return $this->MatinCheck;
    }

    /**
     * Set matinDateCheck
     *
     * @param \DateTime $matinDateCheck
     *
     * @return TraitementJour
     */
    public function setMatinDateCheck($matinDateCheck)
    {
        $this->MatinDateCheck = $matinDateCheck;

        return $this;
    }

    /**
     * Get matinDateCheck
     *
     * @return \DateTime
     */
    public function getMatinDateCheck()
    {
        return $this->MatinDateCheck;
    }

    /**
     * Set midiCheck
     *
     * @param boolean $midiCheck
     *
     * @return TraitementJour
     */
    public function setMidiCheck($midiCheck)
    {
        $this->MidiCheck = $midiCheck;

        return $this;
    }

    /**
     * Get midiCheck
     *
     * @return boolean
     */
    public function getMidiCheck()
    {
        return $this->MidiCheck;
    }

    /**
     * Set midiDateCheck
     *
     * @param \DateTime $midiDateCheck
     *
     * @return TraitementJour
     */
    public function setMidiDateCheck($midiDateCheck)
    {
        $this->MidiDateCheck = $midiDateCheck;

        return $this;
    }

    /**
     * Get midiDateCheck
     *
     * @return \DateTime
     */
    public function getMidiDateCheck()
    {
        return $this->MidiDateCheck;
    }

    /**
     * Set soirCheck
     *
     * @param boolean $soirCheck
     *
     * @return TraitementJour
     */
    public function setSoirCheck($soirCheck)
    {
        $this->SoirCheck = $soirCheck;

        return $this;
    }

    /**
     * Get soirCheck
     *
     * @return boolean
     */
    public function getSoirCheck()
    {
        return $this->SoirCheck;
    }

    /**
     * Set soirDateCheck
     *
     * @param \DateTime $soirDateCheck
     *
     * @return TraitementJour
     */
    public function setSoirDateCheck($soirDateCheck)
    {
        $this->SoirDateCheck = $soirDateCheck;

        return $this;
    }

    /**
     * Get soirDateCheck
     *
     * @return \DateTime
     */
    public function getSoirDateCheck()
    {
        return $this->SoirDateCheck;
    }

    /**
     * Set coucheCheck
     *
     * @param boolean $coucheCheck
     *
     * @return TraitementJour
     */
    public function setCoucheCheck($coucheCheck)
    {
        $this->CoucheCheck = $coucheCheck;

        return $this;
    }

    /**
     * Get coucheCheck
     *
     * @return boolean
     */
    public function getCoucheCheck()
    {
        return $this->CoucheCheck;
    }

    /**
     * Set coucheDateCheck
     *
     * @param \DateTime $coucheDateCheck
     *
     * @return TraitementJour
     */
    public function setCoucheDateCheck($coucheDateCheck)
    {
        $this->CoucheDateCheck = $coucheDateCheck;

        return $this;
    }

    /**
     * Get coucheDateCheck
     *
     * @return \DateTime
     */
    public function getCoucheDateCheck()
    {
        return $this->CoucheDateCheck;
    }

    /**
     * Set autreCheck
     *
     * @param boolean $autreCheck
     *
     * @return TraitementJour
     */
    public function setAutreCheck($autreCheck)
    {
        $this->AutreCheck = $autreCheck;

        return $this;
    }

    /**
     * Get autreCheck
     *
     * @return boolean
     */
    public function getAutreCheck()
    {
        return $this->AutreCheck;
    }

    /**
     * Set autreDateCheck
     *
     * @param \DateTime $autreDateCheck
     *
     * @return TraitementJour
     */
    public function setAutreDateCheck($autreDateCheck)
    {
        $this->AutreDateCheck = $autreDateCheck;

        return $this;
    }

    /**
     * Get autreDateCheck
     *
     * @return \DateTime
     */
    public function getAutreDateCheck()
    {
        return $this->AutreDateCheck;
    }

    /**
     * Set traitement
     *
     * @param \SejourBundle\Entity\Traitement $traitement
     *
     * @return TraitementJour
     */
    public function setTraitement(\SejourBundle\Entity\Traitement $traitement)
    {
        $this->Traitement = $traitement;

        return $this;
    }

    /**
     * Get traitement
     *
     * @return \SejourBundle\Entity\Traitement
     */
    public function getTraitement()
    {
        return $this->Traitement;
    }

    /**
     * Set jour
     *
     * @param \SejourBundle\Entity\Jour $jour
     *
     * @return TraitementJour
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
		
	public function setTraitementMoment($moment)
	{
		if($moment ==1)
		{
			$this->setMatinCheck(true);
			$this->setMatinDateCheck(new \DateTime('now'));
		}
		elseif ( $moment ==2 )
		{
			$this->setMidiCheck(true);
			$this->setMidiDateCheck( new \DateTime('now'));		
		}
		elseif ( $moment ==3 )
		{
			$this->setSoirCheck(true);
			$this->setSoirDateCheck( new \DateTime('now'));		
		}
		elseif ( $moment ==4 )
		{
			$this->setCoucheCheck(true);
			$this->setCoucheDateCheck( new \DateTime('now'));		
		}
		elseif ( $moment ==5 )
		{
			$this->setAutreCheck(true);
			$this->setAutreDateCheck( new \DateTime('now'));		
		}
		
		return $this;
	}
}
