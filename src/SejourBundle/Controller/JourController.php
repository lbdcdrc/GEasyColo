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

class JourController extends Controller
{
	public function jourAction($idSejour, $id){
	$this->container->get('sejour.droits')->AllowedUser($idSejour);
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('SejourBundle:Evenement');
	$prevNextJours = $this->prevNextJours($id, $idSejour);
	$listEvenement = $repository->findBy(
	array('jour' => $id));
	
	$repository2 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:Jour');
	
	$jour=$repository2->findOneBy(array('sejour'=>$idSejour, 'id'=>$id));
	
	if(null === $jour)
	{
		throw new NotFoundHttpException("La journée n'existe pas.");
	}
	
	return $this->render('SejourBundle:Default:jour.html.twig', array(	'Sejour' => $this->getDoctrine()->getManager()->getRepository('SejourBundle:Sejour')->findOneById($idSejour),
																		'listeEvenements' => $listEvenement, 'jour' => $jour, 'nav'=>$prevNextJours,
																	));
	}
	public function jourAddEventAction($idSejour, $id, Request $request){
	$repository2 = $this->getDoctrine()
	->getManager()
	->getRepository('SejourBundle:Jour');
	
	$jour=$repository2->find($id);
		
	$evenement = new Evenement();
	$form   = $this->get('form.factory')->create(EvenementType::class, $evenement, ['Sejour' => $idSejour]);

	if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
	  $em = $this->getDoctrine()->getManager();
	  $evenement->setJour($jour);
	  $em->persist($evenement);
	  $em->flush();
	  $request->getSession()->getFlashBag()->add('notice', 'L\'activité a bien été ajoutée à la journée.');

	  return $this->redirectToRoute('jour_indexjour', array('idSejour' => $idSejour, 'id' => $id));
	}

	return $this->render('SejourBundle:Default:creerevenement.html.twig', array('form' => $form->createView(), 'jour'=> $jour, 'Sejour' => $this->getDoctrine()->getManager()->getRepository('SejourBundle:Sejour')->findOneById($idSejour),));
	}
	public function jourDellEventAction($id, Request $request){
		$repository = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Evenement')
		;
		$repository2 = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:EvenementLies')
		;
		$evenement = $repository->find($id);
			
		if (null === $evenement) {
		  throw new NotFoundHttpException("L'evenement ".$id." n'existe pas.");
		}
		$em = $this->getDoctrine()->getManager();
		if($evenement->getMoment()==4)
		{
			$Lien=$repository2->findOneBy(array('Jour' => $evenement));
			$M1=$Lien->getMatin1();
			$M2=$Lien->getMatin2();
			
			$em	->remove($Lien)
				->remove($M1)
				->remove($M2);
			$em->flush();

		}

		$em->remove($evenement);
		$em->flush();
		
		$request->getSession()->getFlashBag()->add('notice', 'L\'activité a été supprimée.');
		return $this->redirectToRoute('jour_indexjour', array('id' => $evenement->getJour()->getId()));

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
			if($MatinBloque === false)
			{
			$NewMatin1=$form->get('Matin1')->getData();
			$NewMatin2=$form->get('Matin2')->getData();
			}
			$NewAM=$form->get('AM')->getData();
			$NewJour12=$form->get('Jour12')->getData();
			$NewJour=$form->get('Journee')->getData();
			$em = $this->getDoctrine()->getManager();
			
			if($MatinBloque === false)
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
	private function prevNextJours($id, $idSejour)
	{
		$repository = $this->getDoctrine()
			->getManager()
			->getRepository('SejourBundle:Jour');
		
		$jour=$repository->find($id)->getDate();
		$dateDep=$jour;
		$datePrev = $dateDep->modify('-1 day');
		$JourPrev=$repository->findOneBy(array('date'=>$datePrev, 'sejour'=>$idSejour));
		$datePrev = $dateDep->modify('+2 day');
		$JourNext=$repository->findOneBy(array('date'=>$datePrev, 'sejour'=>$idSejour));

		if($JourPrev)
		{
			$JourPrev=$JourPrev->getId();
		}
		if($JourNext)
		{
			$JourNext=$JourNext->getId();
		}
		$datePrev = $dateDep->modify('-1 day');
		return array($JourPrev, $JourNext);
	}
}

