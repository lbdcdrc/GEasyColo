<?php
// src/SejourBundle/Droits/droits.php

namespace SejourBundle\Droits;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Droits
{
	protected $authorizationChecker;

	// Fonction de contrôle d'accès à toute la partie "séjour"
	public function allowedUser($SejourId){
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
		$ListeDir = $repository->findOneBy(array('id'=>$SejourId, 'directeur'=>$Utilisateur->getId()));
		$ListeAnim = $repository2->findOneBy(array('sejour'=>$SejourId, 'user'=>$Utilisateur->getId()));
		
		$Admin = $this->authorizationChecker->isGranted('ROLE_ADMIN');

		if(!$Admin === true && empty($ListeDir)===true && empty($ListeAnim)===true)
		{
			throw new AccessDeniedException('Tu n\'as pas accés à cette page !');
		}
	
		
	}
	private $em;
	protected $currentUser;
	// constructor
	public function __construct(\Doctrine\ORM\EntityManager $em, $user, AuthorizationChecker $authorizationChecker)  {
		$this->em = $em;
		$this->currentUser = $user;
		$this->authorizationChecker = $authorizationChecker;
	}

}
