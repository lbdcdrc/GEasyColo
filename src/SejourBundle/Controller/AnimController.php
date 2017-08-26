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

class AnimController extends Controller
{
	public function listeAnimSejourAction($id, Request $request){
		$this->container->get('sejour.droits')->AllowedUser($id);
		$repository2 = $this->getDoctrine()
							->getManager()
							->getRepository('SejourBundle:Sejour');
		$Sejour = $repository2->findOneBy(array('id' => $id));
		
		$repository = $this->getDoctrine()
							->getManager()
							->getRepository('SejourBundle:AnimSejour');
		$listeAnim = $repository->findBy(array('sejour'=>$Sejour), array('role' => 'desc'));
		
		$listeAnimRecrutable=$this->listeAnimRecrutable($Sejour);
		$em=$this->getDoctrine()->getManager();
		$affectation = new AnimSejour();
		$form   = $this->get('form.factory')->create(RecruterType::class, $affectation, ['listeAnim' => $listeAnimRecrutable]);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			$em = $this->recrutementAnim($affectation, $request, $em, $Sejour );
			$em->flush();
			return $this->redirectToRoute('sejour_equipe', array('id' => $id));
		}

		return $this->render('SejourBundle:sejour:ListeAnimSejour.html.twig', array('Sejour' => $Sejour, 'listeAnim'=>$listeAnim, 'form' => $form->createView(), ));
	}
	public function derecruteAction($id){
		$repository = $this->getDoctrine()
						->getManager()
						->getRepository('SejourBundle:AnimSejour');
		$affectation = $repository->findOneById($id);
		if ($affectation === null) {
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
	public function planningCongesAction($id, Request $request){
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
		$this->container->get('sejour.droits')->AllowedUser($Sejour);
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
					'class' => 'SejourBundle:IdMoment',
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
						'class' => 'SejourBundle:IdMoment',
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
	private function recrutementAnim($affectation, $request, $em, $Sejour )
	{
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADJOINT')) 
		{
			throw new AccessDeniedException('Accès limité à la direction du séjour.');
		}
		if( $affectation->getUser() === null)
		{
			$request->getSession()->getFlashBag()->add('notice', 'Aucun animateur à recruter !');
			return $this->redirectToRoute('sejour_equipe', array('id' => $id));
		}
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
				->getRepository('SejourBundle:IdMoment');
		
		$listeJour = $repository4->findBy(array('sejour'=>$Sejour));
		
		foreach($listeJour as $j){
		$MomentTravail = $repository6->findOneById(1);
		$LigneConges=new AnimConges();
		$LigneConges->setUser($AnimEnCours)
					->setJour($j)
					->setMoment($MomentTravail);
		$em->persist($LigneConges);		
		}
		$request->getSession()->getFlashBag()->add('notice', 'L\'animateur a été recruté !');
		
		return $em;
	}
	
	private function listeAnimRecrutable($Sejour)
	{
		$repository3 = $this->getDoctrine()
							->getManager()
							->getRepository('UserBundle:User');
			
		$listeAnimR = $repository3->animRecrute($Sejour->getId());
		$listeAnimRecrutable=array();
		foreach($listeAnimR as $anim)
			{	
				if ($anim['id'] != $Sejour->getDirecteur()->getId())
				{
					$listeAnimRecrutable[] = $repository3->find($anim['id']);
				}
			}
		return $listeAnimRecrutable;
	}
}

