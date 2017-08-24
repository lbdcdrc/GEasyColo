<?php
 
namespace SejourBundle\Services;
 
use Doctrine\ORM\EntityManager;
 
class EntityManagerForClass
{
    private $em;
 
    public function _construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em	= $em;

    }
}