<?php

namespace SejourBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SejourBundle\Entity\Sejour;
use SejourBundle\Form\SejourType;
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
use SejourBundle\Entity\idMoment;
use SejourBundle\Entity\Soin;
use SejourBundle\Entity\Traitement;
use SejourBundle\Entity\TraitementJour;
use SejourBundle\Form\ActiviteType;
use SejourBundle\Form\ForumCategorieType;
use SejourBundle\Form\ForumMessageType;
use SejourBundle\Form\AffecterType;
use SejourBundle\Form\ModifierAffectationType;
use SejourBundle\Form\ModifierEvenementType;
use SejourBundle\Form\AjoutFicheType;
use SejourBundle\Form\EnfantType;
use SejourBundle\Form\EvenementType;
use SejourBundle\Form\EditActiviteType;
use SejourBundle\Form\ProblemesEnfantType;
use SejourBundle\Form\RecruterType;
use SejourBundle\Form\SoinType;
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

class DefaultController extends Controller
{
	// Fonction de contrôle d'accés à toute la partie "séjour"
	private function AllowedUser($SejourId){
		// Pour accéder :
		// Soit être admin
		// Soit être recruté sur le séjour (en tant que directeur ou anim)
		
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour')
		;
		$repository2 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:AnimSejour')
		;
		$Utilisateur = $this->getUser();
		$ListeDir = $repository->findBy(array('id'=>$SejourId, 'directeur'=>$Utilisateur));
		$ListeAnim = $repository2->findBy(array('sejour'=>$SejourId, 'user'=>$Utilisateur));
				
		if( $ListeDir == null && $ListeAnim == null && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
		{
			throw new AccessDeniedException('Tu n\'as pas accès à cette page !');
		}

		
	}
	// Accueil de la plateforme
    public function IndexAction(){
		
        return $this->render('SejourBundle:Default:index.html.twig');
    }
	// Acceuil espace séjour
	public function IndexSejourAction(){
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour')
		;
		$repository2 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:AnimSejour')
		;
		
		$listSejoursDir = $repository->findByDirecteur($this->getUser());
		$listSejoursAnim = $repository2->findByUser($this->getUser());
		
		$listeSejour = array();
		
		foreach($listSejoursDir as $Sej)
		{
			$listeSejour[] = $Sej;
		}
		foreach($listSejoursAnim as $Sej)
		{
			$listeSejour[] = $Sej->getSejour();
		}
		
        return $this->render('SejourBundle:Default:sejour.html.twig', array('listeSejours' => $listeSejour));
    }
	// Export JSON de la liste des activité d'un utilisateur
	private function table_acti_util() {
	return $this->get('datatable')
				->setEntity("SejourBundle:Activite", "x")                         
				->setFields(
							array(
								"Catégorie" => 'c.categorie',
								"Titre" => 'x.nom',
								"Actions" => 'x.id',
								"_identifier_" => 'x.id')
					)
				->setRenderers(
					array(
						2 => array(
						'view' => 'SejourBundle:FicheActi:actions.html.twig', 
						)
				))
				->addJoin('x.createur', 'a', \Doctrine\ORM\Query\Expr\Join::INNER_JOIN)
				->addJoin('x.categorie', 'c', \Doctrine\ORM\Query\Expr\Join::INNER_JOIN)
				->setWhere(                                                     
				 'a.id = :createur', array('createur' => $this->getUser()->getId()))
				->setGlobalSearch(true); 
	}              
	// Génération du datatable de la liste des activités d'un utilisateur
	public function tableActiUtilAction(){
		return $this->table_acti_util()->execute();                                      
	}
	// Fiches d'activite d'un utilisateur
	public function ActiviteUtilAction(Request $request){
		$this->table_acti_util();
		
		$activite = new Activite();
		$form   = $this->get('form.factory')->create(ActiviteType::class, $activite);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$activite->setCreateur($this->getUser());
			$em->persist($activite);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'La fiche d\'activité a été créée');

		  return $this->redirectToRoute('activite_util');
		}
		
		return $this->render('SejourBundle:FicheActi:indexacti.html.twig', array('form'=>$form->createView()));		
	}
	// Création d'un nouveau séjour
	public function CreerSejourAction(Request $request){
		$sejour = new Sejour();
		$form   = $this->get('form.factory')->create(SejourType::class, $sejour);
		$repository6 = $this->getDoctrine()
					->getManager()
					->getRepository('SejourBundle:idMoment');

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{

		$DateDebut=$sejour->getDateDebut();
		$DateFin=$sejour->getDateFin();
		$sejour->setDirecteur($this->getUser());
		$em = $this->getDoctrine()->getManager();
		$em->persist($sejour);
		$em->flush();
		  

		
		$NbJours = date_diff($DateDebut, $DateFin);
		$Days=$NbJours->format("%a");		
		$DateTravail = $DateDebut;
		
		$Jour = new Jour();
		$Jour->setDate($DateTravail);
		$Jour->setSejour($sejour);
		
		$em->persist($Jour);
		$em->flush();
		$LigneConges=new AnimConges();
		$MomentTravail = $repository6->findOneById(1);
		$LigneConges->setUser($this->getUser())
					->setJour($Jour)
					->setMoment($MomentTravail);
		$em->persist($LigneConges);
		$em->flush();	


		for($i = 1; $i < $Days+1; $i += 1)
		{
			$NewDate = $DateTravail->modify('+1 day');
			$Jour = new Jour();
			$Jour->setDate($NewDate);
			$Jour->setSejour($sejour);		
			$em->persist($Jour);
			$LigneConges=new AnimConges();
			$LigneConges->setUser($this->getUser())
						->setJour($Jour)
						->setMoment($MomentTravail);
			$em->persist($LigneConges);	
		}
		$em->flush();		
		$request->getSession()->getFlashBag()->add('notice', 'Le séjour a bien été créé.');

		return $this->redirectToRoute('sejour_indexsejour');
		}

		return $this->render('SejourBundle:Default:creersejour.html.twig', array('form' => $form->createView(),));
    }
	// Supprimer un séjour
	public function SupprimerSejourAction($id, Request $request){
		    // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Sejour')
    ;

    // On récupère l'entité correspondante à l'id $id
    $sejour = $repository->find($id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $sejour) {
      throw new NotFoundHttpException("Le séjour d'id ".$id." n'existe pas.");
    }
	
	$em = $this->getDoctrine()->getManager();
	$repository2 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:Jour')
	;

	$listJours = $repository2->findBy(array('sejour' => $id));
	
	foreach($listJours as $Jours)
	{
		$em->remove($Jours);
	}
	$em->remove($sejour);
	$em->flush();
	
	
	$request->getSession()->getFlashBag()->add('notice', 'Le séjour a été supprimé.');
	return $this->redirectToRoute('sejour_indexsejour');

	}
	// Edition d'un enfant
	public function editEnfantAction($id, Request $request){
		$repository = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Enfant')
		;
		$enfant = $repository->find($id);
		//verification des droits
		$this->AllowedUser($id);
		if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Accès réservé à la direction');
		}
		// Ici, l'utilisateur est validé
		
		if (null === $enfant)
		{
			throw new NotFoundHttpException("L'enfant ".$id." n'existe pas.");
		}
		
		$form = $this->get('form.factory')->create(EnfantType::class, $enfant, ['edit' => true]);


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'enfant a bien été modifié');
		  return $this->redirectToRoute('enfant_probleme_list', array('id' => $id));
		}		

		return $this->render('SejourBundle:Default:editenfant.html.twig', array('enfant' => $enfant, 'form' => $form->createView(),));
    }
	// Edition d'une activité espace utilisateur
	public function editActiviteUtilAction($idActi, Request $request){
		$repository = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Activite')
		;	
		$activite = $repository->find($idActi);
		
		if (null === $activite)
		{
			throw new NotFoundHttpException("L'activite n'existe pas");
		}

		
		$form = $this->get('form.factory')->create(EditActiviteType::class, $activite);


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'activité a bien été modifié');
		  return $this->redirectToRoute('activite_util');
		}		

		return $this->render('SejourBundle:FicheActi:editfiche.html.twig', array('acti'=>$activite->getId(), 'form' => $form->createView(),));
    }
	// Edition d'une activité dans un séjour
	public function editActiviteSejourAction($id, $idActi, Request $request){
		$repository = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Activite')
		;
		$repository2 = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour')
		;		
		$activite = $repository->find($idActi);
		$Sejour = $repository2->find($id);
		//verification des droits
		$this->AllowedUser($id);
		// Ici, l'utilisateur est validé
		
		if (null === $activite)
		{
			throw new NotFoundHttpException("L'activite n'existe pas");
		}
		if (null === $Sejour)
		{
			throw new NotFoundHttpException("Le sejour n'existe pas");
		}
		
		$form = $this->get('form.factory')->create(EditActiviteType::class, $activite);


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'activité a bien été modifié');
		  return $this->redirectToRoute('sejour_activite', array('id' => $Sejour->getId()));
		}		

		return $this->render('SejourBundle:TableSejourActi:editfiche.html.twig', array('sejour' => $Sejour->getId(), 'acti'=>$activite->getId(), 'form' => $form->createView(),));
    }
	// Supprimer un enfant
	public function supprimerEnfantAction($id, Request $request){
		    // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Enfant')
    ;

    // On récupère l'entité correspondante à l'id $id
    $enfant = $repository->find($id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $enfant) {
      throw new NotFoundHttpException("L'enfant d'id ".$id." n'existe pas.");
    }
	
	$em = $this->getDoctrine()->getManager();
	$em->remove($enfant);
	$em->flush();
	
	
	$request->getSession()->getFlashBag()->add('notice', 'L\'enfant a été supprimé.');
	return $this->redirectToRoute('enfant_indexenfant');

	}
	// Accueil d'un séjour - Listing Jour + Avancement Séjour
	public function AccueilSejourAction($id, Request $request){
	$this->AllowedUser($id);

    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Jour')
    ;
	
	$listJours = $repository->findBy(
	array('sejour' => $id), // Critere
	array('date' => 'asc'));
	
    $repository2 = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Sejour')
    ;
	$sejour = $repository2->find($id);
	

	
	
	return $this->render('SejourBundle:Default:editsejour.html.twig', array('listeJours' => $listJours, 'Sejour' => $sejour));
	}
	// Export JSON de la liste des activité d'un séjour
	private function table_acti($id) {
	$this->AllowedUser($id);
	return $this->get('datatable')
				->setEntity("SejourBundle:Activite", "x")                         
				->setFields(
							array(
								"Créateur" => 'a.PN',
								"Catégorie" => 'c.categorie',
								"Titre" => 'x.nom',
								"Actions" => 'x.id',
								"_identifier_" => 'x.id')
					)
				->setRenderers(
					array(
						3 => array(
						'view' => 'SejourBundle:TableSejourActi:actions.html.twig', 
						'params' => array( 
									'sejour'    => $id,),
						)
				))

				->addJoin('x.sejour', 's', \Doctrine\ORM\Query\Expr\Join::INNER_JOIN)
				->addJoin('x.createur', 'a', \Doctrine\ORM\Query\Expr\Join::INNER_JOIN)
				->addJoin('x.categorie', 'c', \Doctrine\ORM\Query\Expr\Join::INNER_JOIN)
				->setWhere(                                                     
				 's.id = :sejour', array('sejour' => $id))
				->setOrder("c.categorie", "asc")
				->setGlobalSearch(true); 
	}              
	// Génération du datatable de la liste des activités d'un séjour
	public function tableActiAction($id){
		$this->AllowedUser($id);
		return $this->table_acti($id)->execute();                                      
	}
	// Ajout d'une fiche activité d'un utilisateur à un de ses séjour
	public function AjouterFichesActiSejourAction($id, Request $request){
	// Vérification des droits
		$this->AllowedUser($id);
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Activite')
		;
		$Fiches_Acti = $repository->findByCreateur($this->getUser());
		
		$repository2 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour')
		;
		$sejour = $repository2->findOneById($id);

		
		$Fiches_Acti_Non = array();
		
		foreach ($Fiches_Acti as $Fiche)
		{
			$listSej= $Fiche->getSejour();
			$in=false;
			foreach($listSej as $sej)
			{
				if($sej->getId() == $id)
				{
					$in=true;
				}
			}
			if(!$in)
			{
			$Fiches_Acti_Non[]= $Fiche;
			}
		}


		$form = $this->createFormBuilder()
        ->add('activites', EntityType::class, array(
						'class' => 'SejourBundle:Activite',
						'choices' => $Fiches_Acti_Non,
						'choice_label'=>'nom',
						'label'=>'Choix de la fiche',
						'required'=>true,
						))
		->add('Ajouter la fiche au sejour',      SubmitType::class)
        ->getForm();

		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$data = $form->getData();
			$activite=$data['activites'];
			if( $activite == null)
			{
				$request->getSession()->getFlashBag()->add('notice', 'Aucune fiche à ajouter !');
				return $this->redirectToRoute('sejour_activite', array('id' => $id));
			}	
			$acti = $repository->findOneById($activite->getId());
			$em = $this->getDoctrine()->getManager();
			$acti->addSejour($sejour);
			$em->persist($acti);
			$em->flush();
			
			$request->getSession()->getFlashBag()->add('notice', 'La fiche a été ajoutée au séjour !');

		  return $this->redirectToRoute('sejour_activite', array('id' => $id));
		}
	
		return $this->render('SejourBundle:TableSejourActi:ajoutfiche.html.twig', array('form' => $form->createView(), 'sejour'=>$sejour ));
	}
	// Fiches d'activite d'un séjour
	public function FichesActiSejourAction($id, Request $request){
		// Vérification des droits
		$this->AllowedUser($id);
		$this->table_acti($id);
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour')
		;
		$Sejour = $repository->findOneBy(array('id' => $id));
		
		$activite = new Activite();
		$form   = $this->get('form.factory')->create(ActiviteType::class, $activite);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$activite->setCreateur($this->getUser());
			$activite->addSejour($Sejour);
			$em->persist($activite);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'La fiche d\'activité a été créée');

		  return $this->redirectToRoute('sejour_activite', array('id' => $id));
		}
		
        return $this->render('SejourBundle:sejour:ListeActiSejour.html.twig', array('Sejour' => $Sejour, 'form'=>$form->createView()));
    }
	// Suppression d'une activite d'un séjour
	public function SupprFichesActiSejourAction($id, $idActi, Request $request){
		// Vérification des droits
		$this->AllowedUser($id);
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour')
		;
		$repository2 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Activite')
		;
		
		$Sejour = $repository->findOneBy(array('id' => $id));
		$Activite = $repository2->findOneBy(array('id' => $idActi));
		
		if (null === $Sejour) {
			throw new NotFoundHttpException("Le séjour n'existe pas !");
		}
		if (null === $Activite) {
			throw new NotFoundHttpException("La fiche d'activité n'existe pas !");
		}		
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADJOINT') && !$this->getUser()->getId() == $Activite->getCreateur()->getId()) 
		{
			throw new AccessDeniedException('Tu n\'as pas accès à cette page !');	
		}
		
		$em = $this->getDoctrine()->getManager();
		$Activite->removeSejour($Sejour);
		$em->flush();
		$request->getSession()->getFlashBag()->add('notice', 'La fiche d\'activité a été retirée du séjour');
		return $this->redirectToRoute('sejour_activite', array('id' => $id));
    }
	// Forum séjour - Liste catégorie
	public function AccueilForumAction($id, Request $request){
	$this->AllowedUser($id);
	$repository = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:Sejour');
	$Sejour = $repository->findOneBy(array('id' => $id));
	
	$repository2 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:ForumCategorie');
	$ListeCategorie = $repository2->findBy(array('sejour' => $Sejour->getId()));
	
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
		elseif($Cate->getDernierMessage() == null)
		{
			$Notif=$DernierMessageVu->getAccepteNotifications();
			$ListeCategorieAvecVues[]= array(
										'categorie' => $Cate,
										'vu' => true,
										'notif'=>$Notif);
		}
		elseif($DernierMessageVu->getDernierMessageVu() == null)
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
	
	$Categorie = new ForumCategorie();
	$Categorie->setSejour($Sejour);
	$Categorie->setCreateur($this->getUser());
	$form   = $this->get('form.factory')->create(ForumCategorieType::class, $Categorie);

	if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
		$em = $this->getDoctrine()->getManager();
		$em->persist($Categorie);
		$em->flush();
		$request->getSession()->getFlashBag()->add('notice', 'La discussion a bien été créé.');

	  return $this->redirectToRoute('sejour_forum', array('id' => $Sejour->getId()));
	}
	return $this->render('SejourBundle:sejour:Forum.html.twig', array('Sejour' => $Sejour, 'Categorie' => $ListeCategorieAvecVues,  'form' => $form->createView(),));
	}	
	// Forum séjour - Affichage Discussion
	public function DiscussionAction($id, $idForum, $page, Request $request){
		$this->AllowedUser($id);
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		$Sejour = $repository->findOneBy(array('id' => $id));
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumMessage');
		$ListeMessages = $repository2->findBy(array('categorie' => $idForum));

		$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
			$ListeMessages,
			$request->query->getInt('page', $page)/*change the number 1 by the $page parameter*/,
			10/*limit per page*/
		);
		$pagination->setUsedRoute('sejour_discussion');
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumCategorie');
		$Categorie = $repository3->findOneBy(array('id' => $idForum));
		
		$repository4 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ForumUserMessageVu');
		$DernierMessageVu = $repository4->findOneBy(array('user' => $this->getUser(), 'categorie' => $Categorie));
		
		$DernierMessageVu	->setDernierMessageVu($Categorie->getDernierMessage())
							->setNotifie(false);

		
		$Categorie->increaseVues();
		$em = $this->getDoctrine()->getManager();
		$em->flush();	
		$OkNotif=$DernierMessageVu->getAccepteNotifications();
		
		$Message = new ForumMessage();
		$form   = $this->get('form.factory')->create(ForumMessageType::class, $Message);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
			{
				$em = $this->getDoctrine()->getManager();
				$Message->setCategorie($Categorie);
				$Message->setUser($this->getUser());
				$Message->setDateCreation(new \DateTime('now'));
				$em->persist($Message);
				$Categorie->increaseMessages();
				$Categorie->setDernierMessage($Message);
				$em->flush();
				
				$ListeUtilisateur = $repository4->findBy(array('categorie'=>$Categorie));
				
				foreach($ListeUtilisateur as $Utilisateur)
				{
					if($Utilisateur->getNotifie() == false  && $Utilisateur->getAccepteNotifications() == true )
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
				$em->flush();
				$Page=ceil($Categorie->getReponses()/10);
				$request->getSession()->getFlashBag()->add('notice', 'La réponse a été postée !');

			  return $this->redirectToRoute('sejour_discussion', array('id' => $Sejour->getId(), 'idForum'=> $idForum, 'page'=>$Page));
			}	
		return $this->render('SejourBundle:sejour:Messages.html.twig', array('Sejour' => $Sejour,'Categorie'=> $Categorie, 'Messages' => $pagination,  'form' => $form->createView(), 'notif'=>$OkNotif));
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
	private function table_enfants($id) {
		$this->AllowedUser($id);
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE')) {
			return $this->get('datatable')
						->setEntity("SejourBundle:Enfant", "x")                         
						->setFields(
									array(
										"Photo" => 'x.age',
										"Nom"	=> 'x.nom',
										"Prénom" => 'x.prenom',
										"Chambre" => 'x.chambre',
										"Régime Alimentaire" => 'x.regimes',
										"Fiche Enfant" => 'x.problemeencours',
										"_identifier_" => 'x.id')
							)
						->setRenderers(
									array(
										0 => array('view' => 'SejourBundle:TableEnfant:photo.html.twig',),
										5 => array('view' => 'SejourBundle:TableEnfant:problemes.html.twig',),
									)
							)
						->setWhere(                                                     // set your dql where statement
						 'x.sejour = :sejour',array('sejour' => $id))
						->setOrder("x.nom", "asc")
						->setGlobalSearch(true); 
			}
		else{
						return $this->get('datatable')
						->setEntity("SejourBundle:Enfant", "x")                         
						->setFields(
									array(
										"Photo" => 'x.age',
										"Nom"	=> 'x.nom',
										"Prénom" => 'x.prenom',
										"Chambre" => 'x.chambre',
										"Infos"=> 'x.infos',
										"Régime Alimentaire" => 'x.regimes',
										"Fiche Enfant" => 'x.problemeencours',
										"_identifier_" => 'x.id')
							)
						->setRenderers(
									array(
										0 => array('view' => 'SejourBundle:TableEnfant:photo.html.twig',),
										6 => array('view' => 'SejourBundle:TableEnfant:problemes.html.twig',),
									)
							)
						->setWhere(                                                     // set your dql where statement
						 'x.sejour = :sejour',array('sejour' => $id))
						->setOrder("x.nom", "asc")
						->setGlobalSearch(true); 
		}
                       
	}
	public function tableEnfantsAction($id){
		$this->AllowedUser($id);
		return $this->table_enfants($id)->execute();                                      
	}	
	public function listeEnfantSejourAction($id, Request $request){
		$this->AllowedUser($id);
		$this->table_enfants($id);
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		$Sejour = $repository2->findOneBy(array('id' => $id));
		
		$enfant = new Enfant();
		$enfant->setSejour($Sejour);
		$form   = $this->get('form.factory')->create(EnfantType::class, $enfant, ['edit' => false]);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			    if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADJOINT')) {
			  // Sinon on déclenche une exception « Accès interdit »
			  throw new AccessDeniedException('Accès limité à la direction du séjour.');
			}
			$em = $this->getDoctrine()->getManager();
			$em->persist($enfant);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'enfant a bien été créé.');

		  return $this->redirectToRoute('sejour_liste_enfants', array('id' => $id));
		}
	return $this->render('SejourBundle:sejour:ListeEnfantsSejour.html.twig', array('Sejour' => $Sejour, 'form' => $form->createView(),));
	}
	public function listeAnimSejourAction($id, Request $request){
	$this->AllowedUser($id);
	$repository2 = $this->getDoctrine()
						->getManager()
						->getRepository('SejourBundle:Sejour');
	$Sejour = $repository2->findOneBy(array('id' => $id));
	
	$repository = $this->getDoctrine()
						->getManager()
						->getRepository('SejourBundle:AnimSejour');
	$listeAnim = $repository->findBy(array('sejour'=>$Sejour), array('role' => 'desc'));
	
	$repository3 = $this->getDoctrine()
						->getManager()
						->getRepository('UserBundle:User');
		
	$listeAnimR = $repository3->animRecrute($id);
	$listeAnimRecrutable=array();
	foreach($listeAnimR as $anim)
		{	
			if ($anim['id'] != $Sejour->getDirecteur()->getId())
			{
				$listeAnimRecrutable[] = $repository3->find($anim['id']);
			}
		}
	$affectation = new AnimSejour();
	$form   = $this->get('form.factory')->create(RecruterType::class, $affectation, ['listeAnim' => $listeAnimRecrutable]);
	
	if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			    if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADJOINT')) {
			  // Sinon on déclenche une exception « Accès interdit »
			  throw new AccessDeniedException('Accès limité à la direction du séjour.');
			}
			if( $affectation->getUser() === null)
			{
				$request->getSession()->getFlashBag()->add('notice', 'Aucun animateur à recruter !');
				return $this->redirectToRoute('sejour_equipe', array('id' => $id));
			}
			$em = $this->getDoctrine()->getManager();
			$affectation->setSejour($Sejour);
			$AnimEnCours=$affectation->getUser();
			if($affectation->getRole() == 2)
			{
				$AnimEnCours->addRole('ROLE_ASSISTANT_SANITAIRE');
			}
			elseif($affectation->getRole() == 3)
			{
				$AnimEnCours->addRole('ROLE_ADJOINT');
			}
			$em->persist($AnimEnCours);
			$em->persist($affectation);
			$em->flush();
			
			$message = \Swift_Message::newInstance()
			->setSubject('EasyColo - Confirmation de recrutement')
			->setFrom('register@easycolo.fr')
			->setTo($AnimEnCours->getEmail())
			->setBody(
			$this->renderView(
			'SejourBundle:Emails:registration.html.twig',
			array('anim' => $AnimEnCours, 'sejour'=>$Sejour, 'role'=>$affectation->getRole())
			),
			'text/html'
			)
			;

			$this->get('mailer')->send($message);
			
			$repository4 = $this->getDoctrine()
					->getManager()
					->getRepository('SejourBundle:Jour');
			$repository6 = $this->getDoctrine()
					->getManager()
					->getRepository('SejourBundle:idMoment');
			
			$listeJour = $repository4->findBy(array('sejour'=>$Sejour));
			
			foreach($listeJour as $j){
			$MomentTravail = $repository6->findOneById(1);
			$LigneConges=new AnimConges();
			$LigneConges->setUser($AnimEnCours)
						->setJour($j)
						->setMoment($MomentTravail);
			$em->persist($LigneConges);		
			}
			$em->flush();
			
			$request->getSession()->getFlashBag()->add('notice', 'L\'animateur a été recruté !');

		  return $this->redirectToRoute('sejour_equipe', array('id' => $id));
		}

	return $this->render('SejourBundle:sejour:ListeAnimSejour.html.twig', array('Sejour' => $Sejour, 'listeAnim'=>$listeAnim, 'form' => $form->createView(), ));
	}
	public function derecruteAction($id){
		$repository = $this->getDoctrine()
						->getManager()
						->getRepository('SejourBundle:AnimSejour');
		$affectation = $repository->findOneById($id);
		if (null === $affectation) {
			throw new NotFoundHttpException("Affectation inexistante..");
		}
		$em = $this->getDoctrine()->getManager();
		$Sejour = $affectation->getSejour();
		$Anim = $affectation->getUser();
		
		$message = \Swift_Message::newInstance()
			->setSubject('EasyColo - Annulation d\'un recrutement')
			->setFrom('register@easycolo.fr')
			->setTo($Anim->getEmail())
			->setBody(
			$this->renderView(
			'SejourBundle:Emails:derecrutement.html.twig',
			array('anim' => $Anim, 'sejour'=>$Sejour)
			),
			'text/html'
			)
			;

			$this->get('mailer')->send($message);
		$em->remove($affectation);
		$repository5 = $this->getDoctrine()
							->getManager()
							->getRepository('SejourBundle:AnimConges');
			
		$listeJour = $repository5->findBy(array('user'=>$Anim));
		
		foreach($listeJour as $j){
		$em->remove($j);	
		}
		$em->flush();
		return $this->redirectToRoute('sejour_equipe', array('id' => $Sejour->getId()));
	}
	public function activiteAction(){
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Activite')
		;

		$listActivite = $repository->findAll();

        return $this->render('SejourBundle:Default:activite.html.twig', array('listeActivite' => $listActivite));
    }
	public function creerActiviteAction(Request $request){
		$activite = new Activite();
		$form   = $this->get('form.factory')->create(ActiviteType::class, $activite);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
		  $em = $this->getDoctrine()->getManager();
		  $em->persist($activite);
		  $em->flush();

		  $request->getSession()->getFlashBag()->add('notice', 'L\'activite a bien été créé.');

		  return $this->redirectToRoute('activite_indexactivite');
		}

		return $this->render('SejourBundle:Default:creeractivite.html.twig', array('form' => $form->createView(),));
    }
	public function jourAction($id){
		
				    // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Evenement')
    ;
	
	$listEvenement = $repository->findBy(
	array('jour' => $id), // Critere
	array('Moment' => 'asc'));
	
	$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');
	
	$jour=$repository2->find($id);
	
	$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
	
	$Sejour = $jour->getSejour()->getId();

		$listEnfants = $repository3->findBy(
	array('sejour' => $Sejour), // Critere
	array('nom' => 'asc'));
	
	$listInscriptionsComplete=array();
	$listInscriptionsIncomplete=array();
	$listInscriptionsNulle=array();
	
	
	
	foreach(  $listEnfants as $enfant )
	{
		$listeEvEnfant = $enfant->getEvenements();
		$NbMatin1=0;
		$NbMatin2=0;
		$NbAM=0;
		$NbJour12=0;
		$NbJour=0;
		foreach( $listeEvEnfant as $EvEnfant)
		{
			if($EvEnfant->getJour()->getId() == $id)
			{
				if($EvEnfant->getMoment() == 1)
				{
					$NbMatin1+=1;
				}
				elseif($EvEnfant->getMoment() == 2)
				{
					$NbMatin2+=1;
				}
				elseif($EvEnfant->getMoment() == 3)
				{
					$NbAM+=1;
				}
				elseif($EvEnfant->getMoment() == 4)
				{
					$NbJour12+=1;
				}
				elseif($EvEnfant->getMoment() == 5)
				{
					$NbJour+=1;
				}
			}
		}
		
		if( $NbMatin1 == 0 && $NbMatin2 ==0 && $NbAM == 0 && $NbJour12 ==0 && $NbJour==0)
		{
			$listInscriptionsNulle[] = $enfant;
		}
		elseif( (($NbMatin1 == 1 && $NbMatin2 ==1 && $NbAM == 1) xor $NbJour12 ==1) xor $NbJour==1)
		{
			$listInscriptionsComplete[] = $enfant;
		}
		else
		{
			$listInscriptionsIncomplete[] = $enfant;
		}
	}
	
	
		
	return $this->render('SejourBundle:Default:jour.html.twig', array('listeEvenements' => $listEvenement, 'jour' => $jour, 
	'listeEnfantsComplet' => $listInscriptionsComplete,
	'listeEnfantsIncomplet' => $listInscriptionsIncomplete,
	'listeEnfantsNulle' => $listInscriptionsNulle
	
	));
	}
	public function jourAddEventAction($id, Request $request){
	$repository2 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:Jour');
	
	$jour=$repository2->find($id);
		
	$evenement = new Evenement();
	$form   = $this->get('form.factory')->create(EvenementType::class, $evenement);

	if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
	  $em = $this->getDoctrine()->getManager();
	  $evenement->setJour($jour);
	  $em->persist($evenement);
	  $em->flush();
	  
	  if($evenement->getMoment() == 4)
	  {
			$Date=$jour->getDate();
			$Sejour=$jour->getSejour();
			
			$NewDate=$Date;
			$NewDate = $NewDate->modify('+1 day');
			
			$ListeJourDate=$repository2->findBy(
			array('date' => $NewDate),
			array('sejour'=>'asc'));
			
			$NewJour=Null;
			
			foreach($ListeJourDate as $j)
			{
				if($j->getSejour() == $Sejour)
				{
					$NewJour=$j;
				}
			}

		$evenementFilsM1 = new Evenement();
		$evenementFilsM1->setJour($NewJour);
		$evenementFilsM1->setMoment(1);
		$evenementFilsM1->setEstlie(True);
		$evenementFilsM1->setNbPlaces($evenement->getNbPlaces());
		$evenementFilsM1->setActivite($evenement->getActivite());
		$em->persist($evenementFilsM1);
		$em->flush();
		
		$evenementFilsM2 = new Evenement();
		$evenementFilsM2->setJour($NewJour);
		$evenementFilsM2->setMoment(2);
		$evenementFilsM2->setEstlie(True);
		$evenementFilsM2->setNbPlaces($evenement->getNbPlaces());
		$evenementFilsM2->setActivite($evenement->getActivite());
		$em->persist($evenementFilsM2);
		$em->flush();
		
		$Link= new EvenementLies();
		$Link->setJour($evenement);
		$Link->setMatin1($evenementFilsM1);
		$Link->setMatin2($evenementFilsM2);
		$em->persist($Link);
		$em->flush();
			
	  }

	  $request->getSession()->getFlashBag()->add('notice', 'L\'activité a bien été ajoutée à la journée.');

	  return $this->redirectToRoute('jour_indexjour', array('id' => $id));
	}

	return $this->render('SejourBundle:Default:creerevenement.html.twig', array('form' => $form->createView(), 'jour'=> $jour));
	}
	public function jourDellEventAction($id, Request $request){
		    // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Evenement')
    ;
	
	$repository2 = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:EvenementLies')
    ;

    // On récupère l'entité correspondante à l'id $id
    $evenement = $repository->find($id);
	
	$jourId = $evenement->getJour()->getId();

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $evenement) {
      throw new NotFoundHttpException("L'evenement ".$id." n'existe pas.");
    }
	$em = $this->getDoctrine()->getManager();
	if($evenement->getMoment()==4)
	{
		$Lien=$repository2->findOneBy(
			array('Jour' => $evenement));
			$M1=$Lien->getMatin1();
			$M2=$Lien->getMatin2();
			
			$em->remove($Lien);
			$em->flush();
			$em->remove($M1);
			$em->flush();
			$em->remove($M2);
			$em->flush();

	}
	
	

	$em->remove($evenement);
	$em->flush();
	
	
	$request->getSession()->getFlashBag()->add('notice', 'L\'activité a été supprimée.');
	return $this->redirectToRoute('jour_indexjour', array('id' => $jourId));

	}
	public function jourAffecterEnfantAction($idJour, $idEnfant, Request $request){
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');

		$jour=$repository2->find($idJour);
		
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:EvenementLies')
		;

		$enfant=$repository->find($idEnfant);
		$form   = $this->get('form.factory')->create(AffecterType::class, null, ['jour' => $idJour]);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{
			$Matin1=$form->get('Matin1')->getData();
			$Matin2=$form->get('Matin2')->getData();
			$AM=$form->get('AM')->getData();
			$Jour12=$form->get('Jour12')->getData();
			$Journee=$form->get('Journee')->getData();
			$em = $this->getDoctrine()->getManager();
			
			if(($Matin1 || $Matin2 || $AM) && ($Jour12 || $Journee))
			{
				$request->getSession()->getFlashBag()->add('alert', 'Les inscriptions n\'ont pas étés enregistrés (Inscriptions incohérentes)');
				return $this->redirectToRoute('jour_indexjour', array('id' => $idJour));
				
			}
			
			if($Matin1)
			{
				$Matin1->addEnfant($enfant);
				$em->persist($Matin1);
				$em->flush();
			}
			
			if($Matin2)
			{
				$Matin2->addEnfant($enfant);
				$em->persist($Matin2);
				$em->flush();
			}
			
			if($AM)
			{
				$AM->addEnfant($enfant);
				$em->persist($AM);
				$em->flush();
			}
			
			if($Jour12)
			{
				$Jour12->addEnfant($enfant);
				$em->persist($Jour12);
				$em->flush();
				
				$Lien=$repository3->findOneBy(
				array('Jour' => $Jour12));
				$M1=$Lien->getMatin1();
				$M2=$Lien->getMatin2();
				
				$M1->addEnfant($enfant);
				$em->persist($M1);
				$em->flush();
				$M2->addEnfant($enfant);
				$em->persist($M2);
				$em->flush();
			}
			
			if($Journee)
			{
				$Journee->addEnfant($enfant);
				$em->persist($Journee);
				$em->flush();
			}

			$request->getSession()->getFlashBag()->add('notice', 'Les inscriptions ont bien été prises en compte !');
			return $this->redirectToRoute('jour_indexjour', array('id' => $idJour));

		}
		
		
	

		return $this->render('SejourBundle:Default:jouraffecter.html.twig', array('form' => $form->createView(),'jour'=> $jour, 'enfant'=> $enfant));
	}
	public function jourModifierEnfantAction($idJour, $idEnfant, Request $request){
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');

		$jour=$repository2->find($idJour);
		
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:EvenementLies')
		;

		$enfant=$repository->find($idEnfant);
		
		$listeEvEnfant = $enfant->getEvenements();
		$Matin1=Null;
		$Matin2=Null;
		$AM=Null;
		$Jour12=Null;
		$Jour=Null;
		foreach( $listeEvEnfant as $EvEnfant)
		{
			if($EvEnfant->getJour()->getId() == $idJour)
			{
				if($EvEnfant->getMoment() == 1)
				{
					$Matin1=$EvEnfant;
				}
				elseif($EvEnfant->getMoment() == 2)
				{
					$Matin2=$EvEnfant;
				}
				elseif($EvEnfant->getMoment() == 3)
				{
					$AM=$EvEnfant;
				}
				elseif($EvEnfant->getMoment() == 4)
				{
					$Jour12=$EvEnfant;
				}
				elseif($EvEnfant->getMoment() == 5)
				{
					$Jour=$EvEnfant;
				}
			}
		}
		
		$MatinBloque=false;
		if($Matin1)
		{
			$MatinBloque=$Matin1->getEstlie();
		}
		
		$form   = $this->get('form.factory')->create(ModifierAffectationType::class, null, ['jour' => $idJour, 'Matin1' => $Matin1, 'Matin2' => $Matin2, 'AM'=> $AM, 'Jour12'=> $Jour12, 'Journee'=>$Jour]);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{
			if($MatinBloque == false)
			{
			$NewMatin1=$form->get('Matin1')->getData();
			$NewMatin2=$form->get('Matin2')->getData();
			}
			$NewAM=$form->get('AM')->getData();
			$NewJour12=$form->get('Jour12')->getData();
			$NewJour=$form->get('Journee')->getData();
			$em = $this->getDoctrine()->getManager();
			
			if($MatinBloque == false)
			{
			
				if($Matin1 != $NewMatin1)
				{
					if($Matin1)
					{
					$Matin1->removeEnfant($enfant);
					$em->flush();
					}
					if($NewMatin1)
					{
						$NewMatin1->addEnfant($enfant);
						$em->persist($NewMatin1);
						$em->flush();
					}
				}
			
			if($Matin2 != $NewMatin2)
			{
				if($Matin2)
				{
				$Matin2->removeEnfant($enfant);
				$em->flush();
				}
				if($NewMatin2)
				{
					$NewMatin2->addEnfant($enfant);
					$em->persist($NewMatin2);
					$em->flush();
				}
			}
			}
			if($AM != $NewAM)
			{
				if($AM)
				{
				$AM->removeEnfant($enfant);
				$em->flush();
				}
				if($NewAM)
				{
					$NewAM->addEnfant($enfant);
					$em->persist($NewAM);
					$em->flush();
				}
			}
			
			if($Jour12 != $NewJour12)
			{
				if($Jour12)
				{
				$Jour12->removeEnfant($enfant);
				$em->flush();
				
				$Lien=$repository3->findOneBy(
				array('Jour' => $Jour12));
				$M1=$Lien->getMatin1();
				$M2=$Lien->getMatin2();

				$M1->removeEnfant($enfant);
				$em->persist($M1);
				$em->flush();
				$M2->removeEnfant($enfant);
				$em->persist($M2);
				$em->flush();
				
				}
				if($NewJour12)
				{
					$NewJour12->addEnfant($enfant);
					$em->persist($NewJour12);
					$em->flush();
					
					$Lien=$repository3->findOneBy(
					array('Jour' => $NewJour12));
					$M1=$Lien->getMatin1();
					$M2=$Lien->getMatin2();

					$M1->addEnfant($enfant);
					$em->persist($M1);
					$em->flush();
					$M2->addEnfant($enfant);
					$em->persist($M2);
					$em->flush();
				}
			}

			if($Jour != $NewJour)
			{
				if($Jour)
				{
				$Jour->removeEnfant($enfant);
				$em->flush();
				}
				if($NewJour)
				{
					$NewJour->addEnfant($enfant);
					$em->persist($NewJour);
					$em->flush();
				}
			}
			

			$request->getSession()->getFlashBag()->add('notice', 'Les inscriptions ont bien étés modifiés !');
			return $this->redirectToRoute('jour_indexjour', array('id' => $idJour));

		}
		
		
	

		return $this->render('SejourBundle:Default:jourmodifieraffectation.html.twig', array('form' => $form->createView(),'jour'=> $jour, 'enfant'=> $enfant, 'matinbloque'=>$MatinBloque));
	}
	public function problemeEnfantAction($id, Request $request){
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
		$Enfant=$repository2->findOneById($id);
		
		$Sejour=$Enfant->getSejour();
		
		$this->AllowedUser($Sejour);
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ProblemesEnfant');

		$listProblemes = $repository3->findBy(
		array('enfant' => $Enfant), // Critere
		array('date' => 'desc'));
		
		$URLImage = $Enfant->getImage();
		if($URLImage)
		{
			$URLImage=$URLImage->getwebPath();
		}
		
		$probleme = new ProblemesEnfant();
		$probleme->setEncours(true);
		$probleme->setEnfant($Enfant);
		$form   = $this->get('form.factory')->create(ProblemesEnfantType::class, $probleme);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
		  $em = $this->getDoctrine()->getManager();
		  $probleme->setEcrivain($this->getUser());
		  $em->persist($probleme);
		  $em->flush();

		  $request->getSession()->getFlashBag()->add('notice', 'L\'info a été ajoutée !');

		  return $this->redirectToRoute('enfant_probleme_list', array('id' => $id));
		}
		
		
		
		return $this->render('SejourBundle:Default:problemesenfant.html.twig', array('id' => $id,'listeProblemes' => $listProblemes,'Sejour'=>$Sejour, 'form' => $form->createView(),  'enfant' => $Enfant, 'image'=>$URLImage));
		
	}
	public function problemeEnfantCheckAction($id, Request $request){
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ProblemesEnfant');
		
		$Probleme=$repository3->findOneById($id);
		$Probleme->getEnfant()->decreaseProbleme();
		$Enfant=$Probleme->getEnfant()->getId();
		
		$Probleme->setEncours(false);
		$Probleme->setReglepar($this->getUser());
		$Probleme->setDatefin(new \DateTime('now'));
		$em = $this->getDoctrine()->getManager();
		$em-> flush();
		
		return $this->redirectToRoute('enfant_probleme_list', array('id' => $Enfant));
		
	}
	public function EvenementtoPdfAction($idEv) {
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Evenement')
		;

		// On récupère l'entité correspondante à l'id $id
		$evenement = $repository->find($idEv);
		$listeEnfants = $evenement->getEnfant();
		$jour = $evenement->getJour();
		
		$listEnfantImage=array();
		foreach($listeEnfants as $Enf)
		{
			$listEnfantImage[] = array(
										'prenom' => $Enf->getPrenom(),
										'nom' => $Enf->getNom(),
										'age' => $Enf ->getAge(),
										'info' => $Enf -> getInfos(),
										'chambre' => $Enf ->getChambre(),
										'image' => $Enf -> getImage()->getwebPath());

			
		}
		
		return $this->render(
		'SejourBundle:Default:exportpdf.html.twig', 
		//array('listeEnfant' => $listEnfants, 'sejour' => $Sejour )
		array('listeEnfant' => $listEnfantImage, 'jour' => $jour, 'evenement' => $evenement )
		);
		
 		/* $options = new Options();
		// Pour simplifier l'affichage des images, on autorise dompdf à utiliser 
		// des  url pour les nom de  fichier
		$options->set('isRemoteEnabled', TRUE);
		// On crée une instance de dompdf avec  les options définies 
		$dompdf = new Dompdf();
		
		$html = $this->renderView(
		'SejourBundle:Default:exportpdf.html.twig', 
		array('listeEnfant' => $listeEnfants, 'jour' => $jour, 'evenement' => $evenement )
		);
		// On envoie le code html  à notre instance de dompdf
		$dompdf->loadHtml($html);       
		$dompdf->setPaper('A4', 'portrait');		
		// On demande à dompdf de générer le  pdf
		$dompdf->render();
		// On renvoie  le flux du fichier pdf dans une  Response pour l'utilisateur
		$NomFichier='Activite-'.$evenement->getActivite()->getNom().'-'.$evenement->getJour()->getDate()->format('d-m-y').'.pdf';
		return new Response ($dompdf->stream($NomFichier)); */

	}
	// Edition Trombi simple  (Nom - Prénom - Age - Chambre - Photo)
	public function ListingtoPdfAction($idSej) {
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->AllowedUser($idSej);
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');

		$listEnfants = $repository3->findBy(
		array('sejour' => $idSej), // Critere
		array('nom' => 'asc'));
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		
		$Sejour = $repository2->findOneById($idSej);
		$this->AllowedUser($Sejour);
		$listEnfantImage=array();
		foreach($listEnfants as $Enf)
		{
			if($Enf-> getImage())
			{
			$listEnfantImage[] = array(
										'prenom' => $Enf->getPrenom(),
										'nom' => $Enf->getNom(),
										'age' => $Enf ->getAge(),
										'chambre' => $Enf -> getChambre(),
										'image' => $Enf -> getImage()->getwebPath());
			}
			else
			{
			$listEnfantImage[] = array(
							'prenom' => $Enf->getPrenom(),
							'nom' => $Enf->getNom(),
							'age' => $Enf ->getAge(),
							'chambre' => $Enf -> getChambre(),
							'image' => null);	
			}

			
		}
		return $this->render('SejourBundle:Default:listingenfant.html.twig', array('listeEnfant' => $listEnfantImage, 'sejour' => $Sejour ));
	}
	// Edition Trombi complet  (Nom - Prénom - Age - Chambre - Photo - Infos - Régimes)
	public function ListingCompletToPdfAction($idSej) {
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->AllowedUser($idSej);
		if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Accès réservé à la direction');
		}
		// Ici, l'utilisateur est validé
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');

		$listEnfants = $repository3->findBy(
		array('sejour' => $idSej), // Critere
		array('nom' => 'asc'));
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		
		$Sejour = $repository2->findOneById($idSej);
		$this->AllowedUser($Sejour);
		$listEnfantImage=array();
		foreach($listEnfants as $Enf)
		{
			if($Enf-> getImage())
			{
			$listEnfantImage[] = array(
										'prenom' => $Enf->getPrenom(),
										'nom' => $Enf->getNom(),
										'age' => $Enf ->getAge(),
										'chambre' => $Enf -> getChambre(),
										'info' => $Enf -> getInfos(),
										'regime' => $Enf ->getRegimes(),
										'image' => $Enf -> getImage()->getwebPath());
			}
			else
			{
			$listEnfantImage[] = array(
							'prenom' => $Enf->getPrenom(),
							'nom' => $Enf->getNom(),
							'age' => $Enf ->getAge(),
							'chambre' => $Enf -> getChambre(),
							'info' => $Enf -> getInfos(),
							'regime' => $Enf ->getRegimes(),
							'image' => null);	
			}

			
		}
		return $this->render('SejourBundle:Default:listingenfantcomplet.html.twig', array('listeEnfant' => $listEnfantImage, 'sejour' => $Sejour ));
	}
	// Edition Trombi Régimes : Seulement les enfants ayant des régimes alimentaires particuliers
	public function ListingRegimeToPdfAction($idSej) {
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->AllowedUser($idSej);
		if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Accès réservé à la direction');
		}
		// Ici, l'utilisateur est validé
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');

		$listEnfants = $repository3->findBy(
		array('sejour' => $idSej), // Critere
		array('nom' => 'asc'));
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		
		$Sejour = $repository2->findOneById($idSej);
		$this->AllowedUser($Sejour);
		$listEnfantImage=array();
		foreach($listEnfants as $Enf)
		{
			if($Enf-> getRegimes())
			{
				if($Enf-> getImage())
				{
				$listEnfantImage[] = array(
											'prenom' => $Enf->getPrenom(),
											'nom' => $Enf->getNom(),
											'age' => $Enf ->getAge(),
											'regime' => $Enf ->getRegimes(),
											'image' => $Enf -> getImage()->getwebPath());
				}
				else
				{
				$listEnfantImage[] = array(
								'prenom' => $Enf->getPrenom(),
								'nom' => $Enf->getNom(),
								'age' => $Enf ->getAge(),
								'regime' => $Enf ->getRegimes(),
								'image' => null);	
				}
			}
			
		}
		return $this->render('SejourBundle:Default:listingregimes.html.twig', array('listeEnfant' => $listEnfantImage, 'sejour' => $Sejour ));
	}
	// Edition du planning de chaque enfant
	public function PlanningEnfantAction($id){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:EvenementLies')
		;
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');

		$Enfant=$repository->find($id);
		$Sejour = $Enfant->getSejour();
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->AllowedUser($Sejour);
		
		$NomEnfant = $Enfant->getNom();
		$PrenomEnfant = $Enfant->getPrenom();
		$URLImage = $Enfant->getImage();
		if($URLImage)
		{
			$URLImage=$URLImage->getwebPath();
		}
		
		$listeEvEnfant = $Enfant->getEvenements();

		$listeJour = $Enfant->getSejour();
		
		$listeJour = $repository3->findBy(
		array('sejour' => $listeJour), // Critere
		array('date' => 'asc'));
		
		return $this->render('SejourBundle:Default:planningenfant.html.twig', array('listeInscriptions' => $listeEvEnfant, 'listeJours'=> $listeJour, 'nom' => $NomEnfant, 'prenom' => $PrenomEnfant, 'image'=>$URLImage));
		
	}
	public function jourEditEventAction($id, Request $request){
		$repository = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Evenement')
		;

		// On récupère l'entité correspondante à l'id $id
		$evenement = $repository->find($id);
		$jour = $evenement->getJour();
		
		if (null === $evenement)
		{
			throw new NotFoundHttpException("L'evenement ".$id." n'existe pas.");
		}
		
		$form = $this->get('form.factory')->create(ModifierEvenementType::class, $evenement);


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
		  // Inutile de persister ici, Doctrine connait déjà notre annonce
		  $em->flush();

		  $request->getSession()->getFlashBag()->add('notice', 'L\'évènement a bien été modifié');
		   return $this->redirectToRoute('jour_indexjour', array('id' => $jour->getId()));
	}

	return $this->render('SejourBundle:Default:creerevenement.html.twig', array('form' => $form->createView(), 'jour'=> $jour));
    }
	// Edition du planning de chaque enfant
	public function PlanningCongesAction($id, Request $request){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:AnimSejour');

		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');

		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');
		
		$repository4 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:AnimConges');

		$listeJour = $repository3->findBy(
		array('sejour' => $id), // Critere
		array('date' => 'asc'));

		$listeAnim = $repository->findBy(
		array('sejour' => $id), // Critere
		array('role' => 'asc'));

		$Sejour = $repository2->findOneById($id);
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->AllowedUser($Sejour);
		$em = $this->getDoctrine()->getManager();
		$form = $this->createFormBuilder();
		$listeAnimConges=array();
		$Directeur=$Sejour->getDirecteur();
		
		$listeJ=array();
		foreach($listeJour as $Jour){
			$CongesAnim = $repository4->findOneBy(array('jour' => $Jour->getId(), 'user'=> $Directeur->getId()))->getMoment();
			$listeJ[$Jour->getId()]=$CongesAnim;
			$NomForm="A".$Directeur->getId()."J".$Jour->getId();
			$form->add($NomForm, EntityType::class, array(
					'class' => 'SejourBundle:idMoment',
					'choice_label'=>'moment',
					'required'=>true,
					'expanded'=>true,
					'data'=>$CongesAnim,
					));
		}
		$JourDir=array();
		$JourDir[$Directeur->getId()]= $listeJ;
		
				
		foreach($listeAnim as $Anim){
			$listeJ=array();
			foreach($listeJour as $Jour){
				$CongesAnim = $repository4->findOneBy(array('jour' => $Jour->getId(), 'user'=> $Anim->getUser()->getId()))->getMoment();
				$listeJ[$Jour->getId()]=$CongesAnim;
				$NomForm="A".$Anim->getUser()->getId()."J".$Jour->getId();
				$form->add($NomForm, EntityType::class, array(
						'class' => 'SejourBundle:idMoment',
						'choice_label'=>'moment',
						'required'=>true,
						'expanded'=>true,
						'data'=>$CongesAnim,
						));
			}
			$listeAnimConges[$Anim->getUser()->getId()]= $listeJ;
		}
        
		$form->add('Modifier les conges',SubmitType::class);
		
		$form = $form->getForm();
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$data = $form->getData();	
			foreach($listeAnim as $Anim)
			{
				foreach($listeJour as $Jour){
					$CongesAnim = $repository4->findOneBy(array('jour' => $Jour->getId(), 'user'=> $Anim->getUser()->getId()));
					$NomForm="A".$Anim->getUser()->getId()."J".$Jour->getId();
					$Travail=$data[$NomForm];
					$CongesAnim -> setMoment($Travail);				
				}
			}
			foreach($listeJour as $Jour){
				$CongesAnim = $repository4->findOneBy(array('jour' => $Jour->getId(), 'user'=> $Directeur->getId()));
				$NomForm="A".$Directeur->getId()."J".$Jour->getId();
				$Travail=$data[$NomForm];
				$CongesAnim -> setMoment($Travail);
			}
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'Les modifications ont étés enregistrées');
			return $this->redirectToRoute('sejour_planning_conges', array('id' => $Sejour->getId()));	
			
		}
		return $this->render('SejourBundle:Default:planningconges.html.twig', array('listeJours'=> $listeJour,'Directeur'=>$Directeur, 'jourDir'=> $JourDir, 'Sejour'=>$Sejour, 'listeconges'=> $listeAnimConges, 'listeAnim'=>$listeAnim, 'form' => $form->createView()));
		
	}	
	public function RegistreSoinsAction($id, $jour, Request $request){
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');
		
		$repository4 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Soin');

		$Sejour = $repository->findOneById($id);
		// Verification des droits
		// Seul les AS + Directions ont accès à cette page
		$this->AllowedUser($Sejour);
		if( !$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Tu n\'as pas accès à cette page !');
		}
		
		$ListeEnfant = $repository2->findBy(array('sejour'=>$Sejour));
		

		$ListeJour = $repository3->findBy(array('sejour'=>$Sejour, 'SoinValide'=>false));
		$ListeJourComplet = $repository3->findBy(array('sejour'=>$Sejour));
		if($jour == null)
		{
			$JourEnCours = current($ListeJourComplet);
		}
		else
		{
			$JourEnCours = $repository3->findOneById($jour);
		}
		
		$ListeSoins = $repository4->findBy(array('jour'=>$JourEnCours));
			
		
			
		$Soin = new Soin();	
		$Soin->setDate(new \DateTime('now'));	
		$Soin->setUser($this->getUser());
		$form   = $this->get('form.factory')->create(SoinType::class, $Soin, ['ListeEnfant' => $ListeEnfant, 'ListeJour' => $ListeJour]);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($Soin);
			$em->flush();
			$JourNew=$Soin->getJour()->getId();
			$request->getSession()->getFlashBag()->add('notice', 'Les modifications ont étés enregistrées');
			return $this->redirectToRoute('sejour_soins', array('id' => $Sejour->getId(), 'jour'=>$JourNew));	
			
		}
				
		return $this->render('SejourBundle:Soins:RegistreSoins.html.twig', array('Sejour'=>$Sejour, 'form'=>$form->createView(),'ListeJour' => $ListeJour, 'ListeJourComplet' => $ListeJourComplet,  'JourEnCours'=>$JourEnCours, 'ListeSoins'=>$ListeSoins));
		
	}
	public function ClotureSoinsAction($id, $jour, Request $request){
		// Verification des droits
		// Seul le Directeur et les admins ont accès à cette page
		$this->AllowedUser($id);
		if( !$this->get('security.authorization_checker')->isGranted('ROLE_DIRECTEUR') )
		{
			throw new AccessDeniedException('Tu n\'as pas accès à cette page !');
		}
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');
		
		$Jour=$repository->findOneBy(array('id'=>$jour));
		$Jour->setSoinValide(true);
		$Jour->setSoinValideDate(new \DateTime('now'));
		$Jour->setSoinValidePar($this->getUser());
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return $this->redirectToRoute('sejour_soins', array('id' => $id, 'jour'=>$jour));
	}
	public function TraitementAction($id, $jour, Request $request){
		// Verification des droits
		// Seul le Directeur et les admins ont accès à cette page
		$this->AllowedUser($id);
		if( !$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Tu n\'as pas accès à cette page !');
		}
		
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		$Sejour = $repository->findOneById($id);
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
		$ListeEnfant = $repository2->findBy(array('sejour'=>$Sejour), array('prenom' => 'asc'));
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');
		$ListeJour = $repository3->findBy(array('sejour'=>$Sejour));
	
		$repository4 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:TraitementJour');	
		
		
		if($jour == null)
		{
			$JourEnCours = current($ListeJour);
		}
		else
		{
			$JourEnCours = $repository3->findOneById($jour);
		}
		$ListeTraitements = $repository4->traitementsejour($id, $JourEnCours->getId());
		
		if ($request->isMethod('POST')) {
			$data = $request->request->all();
			$NbLigne=$data['nbligne'];
			
			$EnfantId=$data['enfant'];
			
			if (isset($data['matin']))
			{
				$Matin=true;
				$MatinPosologie=$data['matinposologie'];
			}
			else
			{
				$Matin=false;
				$MatinPosologie=null;
			}
			if (isset($data['midi']))
			{
				$MidiPosologie=$data['midiposologie'];
				$Midi=true;
			}
			else
			{
				$Midi=false;
				$MidiPosologie=null;
			}
			if (isset($data['soir']))
			{
				$Soir=true;
				$SoirPosologie=$data['soirposologie'];
			}
			else
			{
				$Soir=false;
				$SoirPosologie=null;
			}
			if (isset($data['couche']))
			{
				$Couche=true;
				$CouchePosologie=$data['coucheposologie'];
			}
			else
			{
				$Couche=false;
				$CouchePosologie=null;
			}
			if (isset($data['autre']))
			{
				$Autre=true;
				$AutrePosologie=$data['autreposologie'];
			}
			else
			{
				$Autre=false;
				$AutrePosologie=null;
			}
			
			$Objet=$data['objet'];
			
			$Traitement=new Traitement();
			$Traitement->setTraitement($Objet);
			$Traitement->setMatin($Matin);
			$Traitement->setMatinPosologie($MatinPosologie);
			$Traitement->setMidi($Midi);
			$Traitement->setMidiPosologie($MidiPosologie);
			$Traitement->setSoir($Soir);
			$Traitement->setSoirPosologie($SoirPosologie);
			$Traitement->setCouche($Couche);
			$Traitement->setCouchePosologie($CouchePosologie);
			$Traitement->setAutre($Autre);
			$Traitement->setAutrePosologie($AutrePosologie);
			
			$Enfant= $repository2->findOneBy(array('id'=>$EnfantId));
			
			$Traitement->setEnfant($Enfant);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($Traitement);
			$em->flush();		
			
			$JourDebut=$data['datedebut'];
			$JourFin=$data['datefin'];
			
			$DateDebut=$repository3->findOneBy(array('id'=>$JourDebut))->getDate();
			$DateFin=$repository3->findOneBy(array('id'=>$JourFin))->getDate();
			


			$NbJours = date_diff($DateDebut, $DateFin);
			$Days=$NbJours->format("%a");		
			$DateTravail = $DateDebut;
			$JourTravail = $repository3->findOneBy(array('date'=>$DateTravail, 'sejour'=>$Sejour->getId()));
			$TraitementJour = new TraitementJour();
			$TraitementJour->setJour($JourTravail);
			$TraitementJour->setTraitement($Traitement);
			
			$TraitementJour->setMatinCheck(false);
			$TraitementJour->setMidiCheck(false);
			$TraitementJour->setSoirCheck(false);
			$TraitementJour->setCoucheCheck(false);
			$TraitementJour->setAutreCheck(false);
			
			$em->persist($TraitementJour);
			$em->flush();
			
			for($i = 1; $i < $Days+1; $i += 1)
			{
				$NewDate = $DateTravail->modify('+1 day');
				$JourTravail = $repository3->findOneBy(array('date'=>$NewDate, 'sejour'=>$Sejour->getId()));
				$TraitementJour = new TraitementJour();
				$TraitementJour->setJour($JourTravail);
				$TraitementJour->setTraitement($Traitement);
				$TraitementJour->setMatinCheck(false);
				$TraitementJour->setMidiCheck(false);
				$TraitementJour->setSoirCheck(false);
				$TraitementJour->setCoucheCheck(false);
				$TraitementJour->setAutreCheck(false);
				$em->persist($TraitementJour);			
			}
			$em->flush();
			if($NbLigne>1)
			{
				for($j = 1; $j < $NbLigne; $j += 1)
				{
					$EnfantId=$data[$j.'enfant'];
					
					if (isset($data[$j.'matin']))
					{
						$Matin=true;
						$MatinPosologie=$data[$j.'matinposologie'];
					}
					else
					{
						$Matin=false;
						$MatinPosologie=null;
					}
					if (isset($data[$j.'midi']))
					{
						$Midi=true;
						$MidiPosologie=$data[$j.'midiposologie'];
					}
					else
					{
						$Midi=false;
						$MidiPosologie=null;
					}
					if (isset($data[$j.'soir']))
					{
						$Soir=true;
						$SoirPosologie=$data[$j.'soirposologie'];
					}
					else
					{
						$Soir=false;
						$SoirPosologie=null;
					}
					if (isset($data[$j.'couche']))
					{
						$Couche=true;
						$CouchePosologie=$data[$j.'coucheposologie'];
					}
					else
					{
						$Couche=false;
						$CouchePosologie=null;
					}
					if (isset($data[$j.'autre']))
					{
						$Autre=true;
						$AutrePosologie=$data[$j.'autreposologie'];
					}
					else
					{
						$Autre=false;
						$AutrePosologie=null;
					}

					$Objet=$data[$j.'objet'];
					
					$Traitement=new Traitement();
					$Traitement->setTraitement($Objet);
					$Traitement->setMatin($Matin);
					$Traitement->setMatinPosologie($MatinPosologie);
					$Traitement->setMidi($Midi);
					$Traitement->setMidiPosologie($MidiPosologie);
					$Traitement->setSoir($Soir);
					$Traitement->setSoirPosologie($SoirPosologie);
					$Traitement->setCouche($Couche);
					$Traitement->setCouchePosologie($CouchePosologie);
					$Traitement->setAutre($Autre);
					$Traitement->setAutrePosologie($AutrePosologie);
					
					$Enfant= $repository2->findOneBy(array('id'=>$EnfantId));
					
					$Traitement->setEnfant($Enfant);
					
					$em = $this->getDoctrine()->getManager();
					$em->persist($Traitement);		
					$JourDebut=$data[$j.'datedebut'];
					$JourFin=$data[$j.'datefin'];
					
					$DateDebut=$repository3->findOneBy(array('id'=>$JourDebut))->getDate();
					$DateFin=$repository3->findOneBy(array('id'=>$JourFin))->getDate();
					


					$NbJours = date_diff($DateDebut, $DateFin);
					$Days=$NbJours->format("%a");		
					$DateTravail = $DateDebut;
					$JourTravail = $repository3->findOneBy(array('date'=>$DateTravail, 'sejour'=>$Sejour->getId()));
					$TraitementJour = new TraitementJour();
					$TraitementJour->setJour($JourTravail);
					$TraitementJour->setTraitement($Traitement);
					
					$TraitementJour->setMatinCheck(false);
					$TraitementJour->setMidiCheck(false);
					$TraitementJour->setSoirCheck(false);
					$TraitementJour->setCoucheCheck(false);
					$TraitementJour->setAutreCheck(false);
					
					$em->persist($TraitementJour);					
					for($i = 1; $i < $Days+1; $i += 1)
					{
						$NewDate = $DateTravail->modify('+1 day');
						$JourTravail = $repository3->findOneBy(array('date'=>$NewDate, 'sejour'=>$Sejour->getId()));
						$TraitementJour = new TraitementJour();
						$TraitementJour->setJour($JourTravail);
						$TraitementJour->setTraitement($Traitement);
						$TraitementJour->setMatinCheck(false);
						$TraitementJour->setMidiCheck(false);
						$TraitementJour->setSoirCheck(false);
						$TraitementJour->setCoucheCheck(false);
						$TraitementJour->setAutreCheck(false);
						$em->persist($TraitementJour);			
					}
	
				}

			}
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', $NbLigne.' traitement(s) ont étés ajoutés !');
			return $this->redirectToRoute('sejour_traitement', array('id' => $Sejour));
		}
		
		return $this->render('SejourBundle:Soins:Traitement.html.twig', array('Sejour'=>$Sejour,'ListeEnfant' => $ListeEnfant, 'ListeJour' => $ListeJour,'Jour'=>$JourEnCours, 'ListeTraitement' => $ListeTraitements));
	}
	public function CheckTraitementAction($id, $jour, $traitement, $moment){

	$repository = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:Sejour');
	$Sejour = $repository->findOneById($id);

	$repository3 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:Jour');
	$Jour = $repository3->findOneById($jour);
	$repository4 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:TraitementJour');
	$Traitement = $repository4->findOneById($traitement);	
	
	if( $moment ==1 )
	{
		$Traitement->setMatinCheck(true);
		$Traitement->setMatinDateCheck( new \DateTime('now'));
	}
	elseif ( $moment ==2 )
	{
		$Traitement->setMidiCheck(true);
		$Traitement->setMidiDateCheck( new \DateTime('now'));		
	}
	elseif ( $moment ==3 )
	{
		$Traitement->setSoirCheck(true);
		$Traitement->setSoirDateCheck( new \DateTime('now'));		
	}
	elseif ( $moment ==4 )
	{
		$Traitement->setCoucheCheck(true);
		$Traitement->setCoucheDateCheck( new \DateTime('now'));		
	}
	elseif ( $moment ==5 )
	{
		$Traitement->setAutreCheck(true);
		$Traitement->setAutreDateCheck( new \DateTime('now'));		
	}

	$em = $this->getDoctrine()->getManager();	
	$em->flush();
	return $this->redirectToRoute('sejour_traitement', array('id' => $Sejour, 'jour' =>$Jour->getId()));
	}
}

