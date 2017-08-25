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

class SejourController extends Controller
{
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
		$this->container->get('sejour.droits')->AllowedUser($id);
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
	// Accueil d'un séjour - Listing Jour + Avancement Séjour
	public function AccueilSejourAction($id, Request $request){
	$this->container->get('sejour.droits')->AllowedUser($id);

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
}

