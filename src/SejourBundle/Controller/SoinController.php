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

class SoinController extends Controller
{
	public function registreSoinsAction($id, $jour, Request $request){
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
		$this->container->get('sejour.droits')->AllowedUser($Sejour);
		if( !$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Tu n\'as pas accès à cette page !');
		}
		
		$ListeEnfant = $repository2->findBy(array('sejour'=>$Sejour));
		

		$ListeJour = $repository3->findBy(array('sejour'=>$Sejour, 'SoinValide'=>false));
		$ListeJourComplet = $repository3->findBy(array('sejour'=>$Sejour));
		if($jour === null)
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
	public function clotureSoinsAction($id, $jour, Request $request){
		// Verification des droits
		// Seul le Directeur et les admins ont accès à cette page
		$this->container->get('sejour.droits')->AllowedUser($id);
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
	public function traitementAction($id, $jour, Request $request){
		// Verification des droits
		// Seul le Directeur et les admins ont accès à cette page
		$this->container->get('sejour.droits')->AllowedUser($id);
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
		
		
		if($jour === null)
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
	public function checkTraitementAction($id, $jour, $traitement, $moment){

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

