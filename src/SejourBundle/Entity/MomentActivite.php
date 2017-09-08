<?php

namespace SejourBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MomentActivite
 *
 * @ORM\Table(name="moment_activite")
 * @ORM\Entity(repositoryClass="SejourBundle\Repository\MomentActiviteRepository")
 */
class MomentActivite
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
     * @ORM\Column(name="NomMoment", type="string", length=255)
     */
    private $nomMoment;


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
     * Set nomMoment
     *
     * @param string $nomMoment
     *
     * @return MomentActivite
     */
    public function setNomMoment($nomMoment)
    {
        $this->nomMoment = $nomMoment;

        return $this;
    }

    /**
     * Get nomMoment
     *
     * @return string
     */
    public function getNomMoment()
    {
        return $this->nomMoment;
    }
}

