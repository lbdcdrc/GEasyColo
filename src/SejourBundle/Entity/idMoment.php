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
}
