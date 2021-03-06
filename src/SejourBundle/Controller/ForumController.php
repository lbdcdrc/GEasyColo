<?php

namespace SejourBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SejourBundle\Entity\Sejour;
use SejourBundle\Form\Type\SejourType;
use SejourBundle\Entity\Enfant;
use SejourBundle\Entity\Jour;
use SejourBundle\Entity\Evenement;
use SejourBundle\Entity\Activite;
use SejourBundle\Entity\ProblemesEnfant;
use SejourBundle\Entity\EvenementLies;
use SejourBundle\Entity\AnimSejour;
use SejourBundle\Entity\ForumCategorie;
use SejourBundle\Entity\ForumMessage;
use SejourBundle\Entity\ForumUserMessageVu;
use SejourBundle\Entity\AnimConges;
use SejourBundle\Entity\IdMoment;
use SejourBundle\Entity\Soin;
use SejourBundle\Entity\Traitement;
use SejourBundle\Entity\TraitementJour;
use SejourBundle\Form\Type\ActiviteType;
use SejourBundle\Form\Type\ForumCategorieType;
use SejourBundle\Form\Type\ForumMessageType;
use SejourBundle\Form\Type\AffecterType;
use SejourBundle\Form\Type\ModifierAffectationType;
use SejourBundle\Form\Type\ModifierEvenementType;
use SejourBundle\Form\Type\AjoutFicheType;
use SejourBundle\Form\Type\EnfantType;
use SejourBundle\Form\Type\EvenementType;
use SejourBundle\Form\Type\ProblemesEnfantType;
use SejourBundle\Form\Type\RecruterType;
use SejourBundle\Form\Type\SoinType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ForumController extends Controller
{
	// Forum séjour - Liste catégorie
	public function accueilForumAction($id, Request $request){
	$em = $this->getDoctrine()->getManager();
	$this->container->get('sejour.droits')->AllowedUser($id);
	$repository = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:Sejour');
	$Sejour = $repository->findOneBy(array('id' => $id));
	
	$repository2 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:ForumCategorie');
	$ListeCategorie = $repository2->findBy(array('sejour' => $Sejour->getId()));

	list($ListeCategorieAvecVues, $em)= $this->listeCategorie($ListeCategorie, $em);

	$Categorie = new ForumCategorie();
	$Categorie	->setSejour($Sejour)
				->setCreateur($this->getUser());
	$form   = $this->get('form.factory')->create(ForumCategorieType::class, $Categorie);

	if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
		$em->persist($Categorie);
		$em->flush();
		$request->getSession()->getFlashBag()->add('notice', 'La discussion a bien été créé.');
		return $this->redirectToRoute('sejour_forum', array('id' => $Sejour->getId()));
	}
	return $this->render('SejourBundle:sejour:Forum.html.twig', array('Sejour' => $Sejour, 'Categorie' => $ListeCategorieAvecVues,  'form' => $form->createView(),));
	}
	// Forum séjour - Supprimer
	public function supprimerAction($id, $idForum){
	$em = $this->getDoctrine()->getManager();
	$this->container->get('sejour.droits')->AllowedUser($id);

	$repository = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:ForumCategorie');
	
	$repository2 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:ForumMessage');

	$repository3 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:ForumUserMessageVu');
	
	$Categorie = $repository->findOneBy(array('sejour' => $id, 'id'=>$idForum));

	
	if(null === $Categorie)
	{
		throw new NotFoundHttpException("La discussion n'existe pas.");
	}
	$Categorie->setDernierMessage(null);
	$em->flush();	
	$ListeMessageVu = $repository3->findBy(array('categorie'=>$Categorie));
	
	foreach($ListeMessageVu as $Mes)
	{
		$em->remove($Mes);
	}
	$em->flush();
	$ListeMessage = $repository2->findBy(array('categorie'=>$Categorie));
	
	foreach($ListeMessage as $Mes)
	{
		$em->remove($Mes);
	}
	$em->flush();
	$em->remove($Categorie);
	$em->flush();
	


	return $this->redirectToRoute('sejour_forum', array('id' => $id));
	}	
	// Forum séjour - Affichage Discussion
	public function discussionAction($id, $idForum, $page, Request $request)
	{
		$this->container->get('sejour.droits')->AllowedUser($id);
		$Sejour = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour')
		->findOneBy(array('id' => $id));
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumMessage');

		$pagination = $this->get('knp_paginator')->paginate($repository2->findBy(array('categorie' => $idForum)),
															$request->query->getInt('page', $page),
															10);													
		$pagination->setUsedRoute('sejour_discussion');
		$Categorie = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumCategorie')
		->findOneBy(array('id' => $idForum));
		
		$repository4 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumUserMessageVu');
		
		$DernierMessageVu = $repository4->findOneBy(array('user' => $this->getUser(), 'categorie' => $Categorie));
		$DernierMessageVu	->setDernierMessageVu($Categorie->getDernierMessage())
							->setNotifie(false);
		$Categorie->increaseVues();
		$em = $this->getDoctrine()->getManager();
		
		$Message = new ForumMessage();
		$form   = $this->get('form.factory')->create(ForumMessageType::class, $Message);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
			{
				list($em, $Page) = $this->nouvelleReponse($em, $Message, $Categorie, $Sejour, $repository4, $request);
				$em->flush();
				return $this->redirectToRoute('sejour_discussion', array('id' => $Sejour->getId(), 'idForum'=> $idForum, 'page'=>$Page));
			}	
		return $this->render('SejourBundle:sejour:Messages.html.twig', array('Sejour' => $Sejour,'Categorie'=> $Categorie, 'Messages' => $pagination,  'form' => $form->createView(), 'notif'=>$DernierMessageVu->getAccepteNotifications()));
	}
	
	public function forumAbonnementAction($id, $forum, Request $request){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumUserMessageVu');
		$AbonnementCate = $repository->findOneBy(array('user' => $this->getUser(), 'categorie' => $forum));
		$Abonnement = $AbonnementCate->getAccepteNotifications();
		if($Abonnement){
			$AbonnementCate->setAccepteNotifications(false);
			$request->getSession()->getFlashBag()->add('notice', 'Les notifications pour ce fil sont désactivées !');
		}
		else{
			$AbonnementCate->setAccepteNotifications(true);
			$request->getSession()->getFlashBag()->add('notice', 'Les notifications pour ce fil sont activées !');
		}
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return $this->redirectToRoute('sejour_forum', array('id' => $id));		
	}
	
	private function nouvelleReponse($em, $Message, $Categorie, $Sejour, $UserRepository, $request)
	{
		$Message->setCategorie($Categorie)
				->setUser($this->getUser())
				->setDateCreation(new \DateTime('now'));
		$em->persist($Message);
		$Categorie->increaseMessages();
		$Categorie->setDernierMessage($Message);	
		$ListeUtilisateur = $UserRepository->findBy(array('categorie'=>$Categorie));
		
		foreach($ListeUtilisateur as $Utilisateur)
		{
			if($Utilisateur->getNotifie() === false  && $Utilisateur->getAccepteNotifications() === true )
			{
				if( $Utilisateur->getUser()->getId() != $this->getUser()->getId() )
				{
				$message = \Swift_Message::newInstance()
				->setSubject('EasyColo - Une réponse a été postée dans le forum !')
				->setFrom('forum@easycolo.fr')
				->setTo($Utilisateur->getUser()->getEmail())
				->setBody(
				$this->renderView(
				'SejourBundle:Emails:reponseforum.html.twig',
				array('forum' => $Utilisateur, 'id' => $Sejour->getId())
				),
				'text/html'
				)
				;

				$this->get('mailer')->send($message);
				$Utilisateur->setNotifie(true);
				}
			}
		}
		$Page=ceil($Categorie->getReponses()/10);
		$request->getSession()->getFlashBag()->add('notice', 'La réponse a été postée !');		
		
		return array($em, $Page);
	}
	private function listeCategorie($ListeCategorie, $em)
	{
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumUserMessageVu');
		$repository4 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumMessage');
		$ListeCategorieAvecVues= array();
		foreach($ListeCategorie as $Cate)
		{
			$DernierMessageVu = $repository3->findOneBy(array('user' => $this->getUser(), 'categorie' => $Cate));
			$page=null;
			$numeroMessage=0;
			$NbMessage=$Cate->getReponses();
			if($DernierMessageVu === null)
			{
				$NewDernier = new ForumUserMessageVu();
				$NewDernier->setUser($this->getUser())
							->setCategorie($Cate)
							->setDernierMessageVu(null)
							->setAccepteNotifications(false)
							->setNotifie(false);
				$em = $this->getDoctrine()->getManager();
				$em->persist($NewDernier);
				$ListeCategorieAvecVues[]= array(
											'categorie' => $Cate,
											'vu' => false,
											'notif'=> false,
											'page' =>1);										
			}
			elseif($Cate->getDernierMessage() === null)
			{
				$Notif=$DernierMessageVu->getAccepteNotifications();
				$ListeCategorieAvecVues[]= array(
											'categorie' => $Cate,
											'vu' => true,
											'notif'=>$Notif,
											'page'=>1);
			}
			elseif($DernierMessageVu->getDernierMessageVu() === null)
			{
				$Notif=$DernierMessageVu->getAccepteNotifications();
				$ListeCategorieAvecVues[]= array(
											'categorie' => $Cate,
											'vu' => false,
											'notif'=>$Notif,
											'page' =>1);
			}
			elseif($DernierMessageVu->getDernierMessageVu() != $Cate->getDernierMessage())
			{
				$Notif=$DernierMessageVu->getAccepteNotifications();
				$ListeMessages=$repository4->findBy(array('categorie' => $Cate->getId()));
				foreach($ListeMessages as $Mes){
					if($Mes->getId() != $Cate->getDernierMessage()->getId()){
						$numeroMessage++;
					}
					else{
					break;
					}
				}
				$Page=ceil($numeroMessage/10);
				$ListeCategorieAvecVues[]= array(
											'categorie' => $Cate,
											'vu' => false,
											'notif'=>$Notif,
											'page'=> $Page);
				
			}
			else
			{
				$Notif=$DernierMessageVu->getAccepteNotifications();
				$ListeCategorieAvecVues[]= array(
											'categorie' => $Cate,
											'notif'=>$Notif,
											'vu' => true,
											'page' =>ceil($NbMessage/10));
			}
			
		}
		$em->flush();
		return array($ListeCategorieAvecVues, $em);
	}
}

