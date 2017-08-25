<?php
// src/SejourBundle/Droits/droits.php

namespace SejourBundle\Droits;

class droits
{
	// Fonction de contr�le d'acc�s � toute la partie "s�jour"
	public function AllowedUser($SejourId){
		// Pour acc�der :
		// Soit �tre admin
		// Soit �tre recrut� sur le s�jour (en tant que directeur ou anim)
		
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
				
		if( $ListeDir == null && $ListeAnim == null && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
		{
			throw new AccessDeniedException('Tu n\'as pas acc�s � cette page !');
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
