<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Traitement
 *
 * @ORM\Table(name="traitement")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\TraitementRepository")
 */
class Traitement
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
     * @var string
     *
     * @ORM\Column(name="Objet", type="text")
     */
    private $traitement;

	 /**
     * @var bool
     *
     * @ORM\Column(name="Matin", type="boolean")
     */
    private $Matin;	
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="MatinPosologie", type="string", length=255, nullable=true)
	 */
	private $MatinPosologie;
	
	 /**
     * @var bool
     *
     * @ORM\Column(name="Midi", type="boolean")
     */
    private $Midi;	
	


	/**
	 * @var string
	 *
	 * @ORM\Column(name="MidiPosologie", type="string", length=255, nullable=true)
	 */
	private $MidiPosologie;

	
	 /**
     * @var bool
     *
     * @ORM\Column(name="Soir", type="boolean")
     */
    private $Soir;	
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="SoirPosologie", type="string", length=255, nullable=true)
	 */
	private $SoirPosologie;

	 /**
     * @var bool
     *
     * @ORM\Column(name="Couche", type="boolean")
     */
    private $Couche;	
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="CouchePosologie", type="string", length=255, nullable=true)
	 */
	private $CouchePosologie;

	 /**
     * @var bool
     *
     * @ORM\Column(name="Autre", type="boolean")
     */
    private $Autre;	

	/**
	 * @var string
	 *
	 * @ORM\Column(name="AutrePosologie", type="string", length=255, nullable=true)
	 */
	private $AutrePosologie;


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
     * Set traitement
     *
     * @param string $traitement
     *
     * @return Traitement
     */
    public function setTraitement($traitement)
    {
        $this->traitement = $traitement;

        return $this;
    }

    /**
     * Get traitement
     *
     * @return string
     */
    public function getTraitement()
    {
        return $this->traitement;
    }

    /**
     * Set matin
     *
     * @param boolean $matin
     *
     * @return Traitement
     */
    public function setMatin($matin)
    {
        $this->Matin = $matin;

        return $this;
    }

    /**
     * Get matin
     *
     * @return boolean
     */
    public function getMatin()
    {
        return $this->Matin;
    }

    /**
     * Set matinPosologie
     *
     * @param string $matinPosologie
     *
     * @return Traitement
     */
    public function setMatinPosologie($matinPosologie)
    {
        $this->MatinPosologie = $matinPosologie;

        return $this;
    }

    /**
     * Get matinPosologie
     *
     * @return string
     */
    public function getMatinPosologie()
    {
        return $this->MatinPosologie;
    }

    /**
     * Set midi
     *
     * @param boolean $midi
     *
     * @return Traitement
     */
    public function setMidi($midi)
    {
        $this->Midi = $midi;

        return $this;
    }

    /**
     * Get midi
     *
     * @return boolean
     */
    public function getMidi()
    {
        return $this->Midi;
    }

    /**
     * Set midiPosologie
     *
     * @param string $midiPosologie
     *
     * @return Traitement
     */
    public function setMidiPosologie($midiPosologie)
    {
        $this->MidiPosologie = $midiPosologie;

        return $this;
    }

    /**
     * Get midiPosologie
     *
     * @return string
     */
    public function getMidiPosologie()
    {
        return $this->MidiPosologie;
    }

    /**
     * Set soir
     *
     * @param boolean $soir
     *
     * @return Traitement
     */
    public function setSoir($soir)
    {
        $this->Soir = $soir;

        return $this;
    }

    /**
     * Get soir
     *
     * @return boolean
     */
    public function getSoir()
    {
        return $this->Soir;
    }

    /**
     * Set soirPosologie
     *
     * @param string $soirPosologie
     *
     * @return Traitement
     */
    public function setSoirPosologie($soirPosologie)
    {
        $this->SoirPosologie = $soirPosologie;

        return $this;
    }

    /**
     * Get soirPosologie
     *
     * @return string
     */
    public function getSoirPosologie()
    {
        return $this->SoirPosologie;
    }

    /**
     * Set couche
     *
     * @param boolean $couche
     *
     * @return Traitement
     */
    public function setCouche($couche)
    {
        $this->Couche = $couche;

        return $this;
    }

    /**
     * Get couche
     *
     * @return boolean
     */
    public function getCouche()
    {
        return $this->Couche;
    }

    /**
     * Set couchePosologie
     *
     * @param string $couchePosologie
     *
     * @return Traitement
     */
    public function setCouchePosologie($couchePosologie)
    {
        $this->CouchePosologie = $couchePosologie;

        return $this;
    }

    /**
     * Get couchePosologie
     *
     * @return string
     */
    public function getCouchePosologie()
    {
        return $this->CouchePosologie;
    }

    /**
     * Set autre
     *
     * @param boolean $autre
     *
     * @return Traitement
     */
    public function setAutre($autre)
    {
        $this->Autre = $autre;

        return $this;
    }

    /**
     * Get autre
     *
     * @return boolean
     */
    public function getAutre()
    {
        return $this->Autre;
    }

    /**
     * Set autrePosologie
     *
     * @param string $autrePosologie
     *
     * @return Traitement
     */
    public function setAutrePosologie($autrePosologie)
    {
        $this->AutrePosologie = $autrePosologie;

        return $this;
    }

    /**
     * Get autrePosologie
     *
     * @return string
     */
    public function getAutrePosologie()
    {
        return $this->AutrePosologie;
    }

    /**
     * Set enfant
     *
     * @param \SejourBundle\Entity\Enfant $enfant
     *
     * @return Traitement
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
}
