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

class EnfantController extends Controller
{
	// Edition d'un enfant
	public function editEnfantAction($idSejour, $id, Request $request){
		$repository = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Enfant')
		;
		$enfant = $repository->find($id);
		//verification des droits
		$this->container->get('sejour.droits')->AllowedUser($idSejour);
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
			if(!$enfant->getImage()===null){
				echo($enfant->getImage()->getwebPath());
				toto;
			$cacheManager = $this->get('liip_imagine.cache.manager');
			$cacheManager->remove($enfant->getImage()->getwebPath(), 'my_thumb');
			$cacheManager->remove($enfant->getImage()->getwebPath(), 'md_thumb');
			$cacheManager->remove($enfant->getImage()->getwebPath(), 'lg_thumb');
			$this->container->get('liip_imagine.controller')->filterAction($request, $enfant->getImage()->getwebPath(), 'my_thumb');
			$this->container->get('liip_imagine.controller')->filterAction($request, $enfant->getImage()->getwebPath(), 'md_thumb');
			$this->container->get('liip_imagine.controller')->filterAction($request, $enfant->getImage()->getwebPath(), 'lg_thumb'); }
	
			$request->getSession()->getFlashBag()->add('notice', 'L\'enfant a bien été modifié');
		  return $this->redirectToRoute('enfant_probleme_list', array('id' => $id));
		}		

		return $this->render('SejourBundle:Default:editenfant.html.twig', array('enfant' => $enfant,'sejour' => $idSejour, 'form' => $form->createView(),));
    }
	// Supprimer un enfant
	public function supprimerEnfantAction($id,$idSejour, Request $request){
	$this->container->get('sejour.droits')->AllowedUser($idSejour);
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Enfant');

    // On récupère l'entité correspondante à l'id $id
    $enfant = $repository->find($id);
	$enfant = $repository->findOneBy(array('sejour' => $idSejour, 'id'=> $id));

	
    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $enfant) {
      throw new NotFoundHttpException("L'enfant d'id ".$id." n'existe pas.");
    }

	$em = $this->getDoctrine()->getManager();
	$this->removeAllAboutEnfant($enfant->getId());
	$em->remove($enfant);
	$em->flush();
	
	
	$request->getSession()->getFlashBag()->add('notice', 'L\'enfant a été supprimé.');
	return $this->redirectToRoute('sejour_liste_enfants', array('id' => $idSejour));

	}
	private function tableEnfants($id) {
		$this->container->get('sejour.droits')->AllowedUser($id);
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
		$this->container->get('sejour.droits')->AllowedUser($id);
		return $this->tableEnfants($id)->execute();                                      
	}	
	public function listeEnfantSejourAction($id, Request $request){
		$this->container->get('sejour.droits')->AllowedUser($id);
		$this->tableEnfants($id);
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Sejour');
		$Sejour = $repository2->findOneBy(array('id' => $id));
		
		$enfant = new Enfant();
		$enfant->setSejour($Sejour);
		$form   = $this->get('form.factory')->create(EnfantType::class, $enfant, ['edit' => false]);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADJOINT')) {
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
	// Edition du planning de chaque enfant
	public function planningEnfantAction($id){
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
		$this->container->get('sejour.droits')->AllowedUser($Sejour);
		
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
	public function problemeEnfantAction($id, Request $request){
		
		$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Enfant');
		$Enfant=$repository2->findOneById($id);
		
		$this->container->get('sejour.droits')->AllowedUser($Enfant->getSejour());
		
		$repository3 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:ProblemesEnfant');

		$listProblemes = $repository3->findBy(
		array('enfant' => $Enfant), // Critere
		array('date' => 'desc'));
		
		$URLImage = $this->image($Enfant);

		$probleme = new ProblemesEnfant();
		$probleme->setEncours(true)
				->setEnfant($Enfant);
		$form   = $this->get('form.factory')->create(ProblemesEnfantType::class, $probleme);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
		  $em = $this->getDoctrine()->getManager();
		  $probleme->setEcrivain($this->getUser());
		  $em->persist($probleme);
		  $em->flush();

		  $request->getSession()->getFlashBag()->add('notice', 'L\'info a été ajoutée !');

		  return $this->redirectToRoute('enfant_probleme_list', array('id' => $id));
		}
		
		
		
		return $this->render('SejourBundle:Default:problemesenfant.html.twig', array('id' => $id,'listeProblemes' => $listProblemes,'Sejour'=>$Enfant->getSejour(), 'form' => $form->createView(),  'enfant' => $Enfant, 'image'=>$URLImage));
		
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
	
	private function image($Enfant)
	{
		if($Enfant->getImage())
		{
			return $Enfant->getImage()->getwebPath();
		}
		else
		{
			return null;
		}
	}
	
	private function removeAllAboutEnfant($Enfant)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()
			->getManager()
			->getRepository('SejourBundle:ProblemesEnfant');
		$repository2 = $this->getDoctrine()
			->getManager()
			->getRepository('SejourBundle:Soin');
		$repository3 = $this->getDoctrine()
			->getManager()
			->getRepository('SejourBundle:Traitement');	  
		$repository4 = $this->getDoctrine()
			->getManager()
			->getRepository('SejourBundle:TraitementJour');	
		$repository5 = $this->getDoctrine()
			->getManager()
			->getRepository('SejourBundle:Image');	
		$repository6 = $this->getDoctrine()
			->getManager()
			->getRepository('SejourBundle:Enfant');

		$ListeTraitementEnfant = $repository3->findBy(array("enfant" => $Enfant));
		
		foreach($ListeTraitementEnfant as $Traitement)
		{
			$ListeTraitementJourEnfant = $repository4->findBy(array("traitement" => $Traitement->getId()));
			foreach($ListeTraitementJourEnfant as $TraitementJour)
			{
				$em->remove($TraitementJour);
			}
			$em->remove($Traitement);
		}
		$em->flush();
		
		$ListeSoin = $repository2 -> findby(array("enfant" => $Enfant));
		
		foreach($ListeSoin as $Soin)
		{
			$em->remove($Soin);
		}
		$em->flush();
		
		$ListeProblemeEnfant = $repository->findby(array("enfant"=>$Enfant));
		
		foreach($ListeProblemeEnfant as $Probleme)
		{
			$em->remove($Probleme);
		}
		$em->flush();
		$enf=$repository6->findOneBy(array("id"=>$Enfant));
		$enf->setImage(null);
		$Image = $repository5->findOneBy(array("id"=>$repository6->findOneBy(array("id"=>$Enfant))->getImage()));
		if($Image)
		{
			$em->remove($Image);
		}
		$em->flush();
	}
}

