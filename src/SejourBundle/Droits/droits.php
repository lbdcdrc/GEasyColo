<?php
// src/SejourBundle/Droits/droits.php

namespace SejourBundle\Droits;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class droits
{
	// Fonction de contrôle d'accès à toute la partie "séjour"
	public function AllowedUser($SejourId){
		// Pour accéder :
		// Soit être admin
		// Soit être recruté sur le séjour (en tant que directeur ou anim)
		
		$repository = $this
		  ->em
		  ->getRepository('SejourBundle:Sejour')
		;
		$repository2 = $this
		->em
		->getRepository('SejourBundle:AnimSejour')
		;
		$Utilisateur = $this->currentUser;
		$ListeDir = $repository->findBy(array('id'=>$SejourId, 'directeur'=>$Utilisateur));
		$ListeAnim = $repository2->findBy(array('sejour'=>$SejourId, 'user'=>$Utilisateur));
				
		if( $ListeDir === null && $ListeAnim === null && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
		{
			throw new AccessDeniedException('Tu n\'as pas accés à cette page !');
		}
		
		
	}
	private $em;
	protected $currentUser;
	// constructor
	public function __construct(\Doctrine\ORM\EntityManager $em, $user)  {
		$this->em = $em;
		$this->currentUser = $user;
	}

}
