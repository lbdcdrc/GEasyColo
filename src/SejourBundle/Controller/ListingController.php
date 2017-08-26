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

class ListingController extends Controller
{
	public function evenementToPdfAction($idEv) {
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
	public function listingToPdfAction($idSej) {
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->container->get('sejour.droits')->AllowedUser($idSej);
		
		list($listEnfantImage, $Sejour)=$this->listingSejour($idSej);
		return $this->render('SejourBundle:Default:listingenfant.html.twig', array('listeEnfant' => $listEnfantImage, 'sejour' => $Sejour ));
	}
	// Edition Trombi complet  (Nom - Prénom - Age - Chambre - Photo - Infos - Régimes)
	public function listingCompletToPdfAction($idSej) {
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->container->get('sejour.droits')->AllowedUser($idSej);
		if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Accès réservé à la direction');
		}
		// Ici, l'utilisateur est validé
		list($listEnfantImage, $Sejour)=$this->listingSejour($idSej, 'Complet');

		return $this->render('SejourBundle:Default:listingenfantcomplet.html.twig', array('listeEnfant' => $listEnfantImage, 'sejour' => $Sejour ));
	}
	// Edition Trombi Régimes : Seulement les enfants ayant des régimes alimentaires particuliers
	public function listingRegimeToPdfAction($idSej) {
		// Verification des droits
		// Toutes l'équipe du séjour + Admin ont les droits
		$this->container->get('sejour.droits')->AllowedUser($idSej);
		if(!$this->get('security.authorization_checker')->isGranted('ROLE_ASSISTANT_SANITAIRE') )
		{
			throw new AccessDeniedException('Accès réservé à la direction');
		}
		// Ici, l'utilisateur est validé
		list($listEnfantImage, $Sejour)=$this->listingSejour($idSej, 'Regime');
		return $this->render('SejourBundle:Default:listingregimes.html.twig', array('listeEnfant' => $listEnfantImage, 'sejour' => $Sejour ));
	}
	
	private function listingSejour($idSej, $ListingType="Normal")
	{
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
		$this->container->get('sejour.droits')->AllowedUser($Sejour);
		$listEnfantImage=array();
		foreach($listEnfants as $Enf)
		{
			$array['prenom'] = $Enf->getPrenom();
			$array['nom'] = $Enf->getNom();
			$array['age'] = $Enf ->getAge();
			$array['chambre'] = $Enf -> getChambre();
										
			if($Enf-> getImage())
			{
			$array['image'] = $Enf -> getImage()->getwebPath();
			}
			else
			{
			$array['image'] = null ;
			}
			
			if($ListingType === 'Complet')
			{
			$array['info'] = $Enf -> getInfos();
			$array['regime'] = $Enf ->getRegimes();
			}
			if($ListingType === 'Regime')
			{
				if($Enf->getRegimes() !== null)
				{
					$array['regime'] = $Enf ->getRegimes();
					$listEnfantImage[]= $array;
				}
			}
			else
			{
				$listEnfantImage[]= $array;
			}
			
		}
		return array($listEnfantImage,$Sejour);
	}
}

