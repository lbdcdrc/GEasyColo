<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvenementLies
 *
 * @ORM\Table(name="evenement_lies")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\EvenementLiesRepository")
 */
class EvenementLies
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
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Evenement")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $Jour;
	
	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Evenement")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $Matin1;
	
	/**
	* @ORM\ManyToOne(targetEntity="SejourBundle\Entity\Evenement")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $Matin2;


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
     * Set jour
     *
     * @param \SejourBundle\Entity\Evenement $jour
     *
     * @return EvenementLies
     */
    public function setJour(\SejourBundle\Entity\Evenement $jour)
    {
        $this->Jour = $jour;

        return $this;
    }

    /**
     * Get jour
     *
     * @return \SejourBundle\Entity\Evenement
     */
    public function getJour()
    {
        return $this->Jour;
    }

    /**
     * Set matin1
     *
     * @param \SejourBundle\Entity\Evenement $matin1
     *
     * @return EvenementLies
     */
    public function setMatin1(\SejourBundle\Entity\Evenement $matin1)
    {
        $this->Matin1 = $matin1;

        return $this;
    }

    /**
     * Get matin1
     *
     * @return \SejourBundle\Entity\Evenement
     */
    public function getMatin1()
    {
        return $this->Matin1;
    }

    /**
     * Set matin2
     *
     * @param \SejourBundle\Entity\Evenement $matin2
     *
     * @return EvenementLies
     */
    public function setMatin2(\SejourBundle\Entity\Evenement $matin2)
    {
        $this->Matin2 = $matin2;

        return $this;
    }

    /**
     * Get matin2
     *
     * @return \SejourBundle\Entity\Evenement
     */
    public function getMatin2()
    {
        return $this->Matin2;
    }
}
