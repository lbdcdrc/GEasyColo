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
use SejourBundle\Form\Type\ActiviteType;
use SejourBundle\Form\Type\ForumCategorieType;
use SejourBundle\Form\Type\ForumMessageType;
use SejourBundle\Form\Type\AffecterType;
use SejourBundle\Form\Type\ModifierAffectationType;
use SejourBundle\Form\Type\ModifierEvenementType;
use SejourBundle\Form\Type\AjoutFicheType;
use SejourBundle\Form\Type\EnfantType;
use SejourBundle\Form\Type\EvenementType;
use SejourBundle\Form\Type\EditActiviteType;
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

class ActiviteController extends Controller
{
	// Export JSON de la liste des activité d'un utilisateur
	private function tableActiUtil() {
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
		return $this->tableActiUtil()->execute();                                      
	}
	// Fiches d'activite d'un utilisateur
	public function activiteUtilAction(Request $request){
		$this->tableActiUtil();
		
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
	// Export JSON de la liste des activité d'un séjour
	private function tableActi($id) {
	 $this->container->get('sejour.droits')->AllowedUser($id);
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
		$this->container->get('sejour.droits')->AllowedUser($id);
		return $this->tableActi($id)->execute();                                      
	}
	// Ajout d'une fiche activité d'un utilisateur à un de ses séjour
	public function ajouterFichesActiSejourAction($id, Request $request){
	// Vérification des droits
		$this->container->get('sejour.droits')->AllowedUser($id);
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
			if( $activite === null)
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
	public function fichesActiSejourAction($id, Request $request){
		// Vérification des droits
		 $this->container->get('sejour.droits')->AllowedUser($id);
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
	public function supprFichesActiSejourAction($id, $idActi, Request $request){
		// Vérification des droits
		 $this->container->get('sejour.droits')->AllowedUser($id);
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
}

