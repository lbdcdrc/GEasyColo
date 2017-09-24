<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Form\Type\RolesType;
use UserBundle\Form\Type\ProfileType;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonController extends Controller
{
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listeUsersAction(Request $req){
		$repository = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('UserBundle:User');
		$listUser = $repository->findAll();
		
		$listDef = array("data"=>array());
		foreach($listUser as $ev)
		{
			$eve= array(
			"<center>".$ev->getUsername()."</center>",
			$this->renderView('AdminBundle:TableUtilisateurs:etatcivil.html.twig', array( 'age'=> $ev->getAge(),'nom' => $ev->getNom(),'prenom' => $ev->getPrenom(),'naissance' => $ev->getNaissance(),)),
			$this->renderView('AdminBundle:TableUtilisateurs:coordonnees.html.twig', array( 'mail'=> $ev->getEmail(),'telephone' => $ev->getTelephone(),)),
			$this->renderView('AdminBundle:TableUtilisateurs:qualifications.html.twig', array( 'diplome'=> $ev->getDiplome()->getNom(),'psc1'=> $ev->getPsc1(),'sb' => $ev->getSb(),)),
			$this->renderView('AdminBundle:TableUtilisateurs:roles.html.twig', array( 'id'=> $ev->getId(),'nom' => $ev->getNom(),'prenom' => $ev->getPrenom(),'username' => $ev->getUsername(),)),
			);
				
			array_push($listDef["data"], $eve);
		}
		
		$encoders = array(new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$response = new Response($serializer->serialize($listDef, 'json'));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listeSejoursAction(Request $req){
		$repository = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Sejour');
		$listSejour = $repository->findAll();
		$repository2 = $this->getDoctrine()
				  ->getManager()
				  ->getRepository('SejourBundle:AnimSejour');
		$listDef = array("data"=>array());
		foreach($listSejour as $sej)
		{
			$NbUsers = count($repository2->findBy(array('sejour'=>$sej)));
			$eve= array(
			"<center>".$sej->getNomThema()."</center>",
			"<center><a href='".$this->generateUrl('voir_profil', array('id'=> $sej->getDirecteur()->getId()), true)."'>".$sej->getDirecteur()->getPN()."</a></center>",
			"<center>".$sej->getDateDebut()->format('d/m/Y')."</center>",
			"<center>".$sej->getDateFin()->format('d/m/Y')."</center>",
			"<center>".$NbUsers."</center>",
			"<center>".$sej->getNbEnfants()."</center>",
			"<center><a class='btn' href=".$this->generateUrl('voir_sejours', array('id'=> $sej->getId()), true)."><i class='icon-search'></i></a></center>"
			);
				
			array_push($listDef["data"], $eve);
		}
		
		$encoders = array(new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$response = new Response($serializer->serialize($listDef, 'json'));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listeEnfantsSejoursAction($id, Request $request){
		$repository = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Enfant');
		$listEnfant = $repository->findBySejour($id);
		
		$listDef = array("data"=>array());
		foreach($listEnfant as $enf)
		{
			
			if(!($enf->getImage()===null))
			{
				 $img='<img src="'.$this->container->get('liip_imagine.controller')->filterAction($request, $enf->getImage()->getwebPath2(), 'lg_thumb')->headers->get('location') .'" alt="'.$enf->getPrenom().'.jpg" width="70">';
			}
			else
			{
				$img='<img src="/anonyme.jpg" alt="'.$enf->getPrenom().'.jpg" width="70">';			
			}
			
			$eve= array(
			"<center>".$img."</center>",
			"<center>".$enf->getNom()."</center>",
			"<center>".$enf->getPrenom()."</center>",
			"<center>".$enf->getAge()." ans</center>",
			"<center>".$enf->getProblemeencours()."</center>",
			);
				
			array_push($listDef["data"], $eve);
		}
		
		$encoders = array(new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$response = new Response($serializer->serialize($listDef, 'json'));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}	
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listeEnfantsAction(Request $request){
		$repository = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Enfant');
		$listEnfant = $repository->findAll();
		
		$listDef = array("data"=>array());
		foreach($listEnfant as $enf)
		{
			
			if(!($enf->getImage()===null))
			{
				 $img='<img src="'.$this->container->get('liip_imagine.controller')->filterAction($request, $enf->getImage()->getwebPath2(), 'lg_thumb')->headers->get('location') .'" alt="'.$enf->getPrenom().'.jpg" width="70">';
			}
			else
			{
				$img='<img src="/anonyme.jpg" alt="'.$enf->getPrenom().'.jpg" width="70">';			
			}
			
			$eve= array(
			"<center>".$img."</center>",
			"<center>".$enf->getSejour()->getNomThema()."</center>",
			"<center>".$enf->getNom()."</center>",
			"<center>".$enf->getPrenom()."</center>",
			"<center>".$enf->getAge()."</center>",
			"<center>".$enf->getProblemeencours()."</center>",
			);
				
			array_push($listDef["data"], $eve);
		}
		
		$encoders = array(new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$response = new Response($serializer->serialize($listDef, 'json'));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listeActivitesAction(Request $request){
		$repository = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Activite');
		$listActivite = $repository->findAll();
		
		$listDef = array("data"=>array());
		foreach($listActivite as $act)
		{
			$eve= array(
			"<center>".$act->getNom()."</center>",
			"<center>".$act->getCategorie()->getCategorie()."</center>",
			"<center>".$act->getCreateur()->getPN()."</center>",
			"<center>".$act->getNbAnim()."</center>",
			"<center>".$act->getNbEnfantMin()."</center>",
			"<center>".$act->getNbEnfantMax()."</center>",
			);
				
			array_push($listDef["data"], $eve);
		}
		
		$encoders = array(new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$response = new Response($serializer->serialize($listDef, 'json'));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listeActivitesSejoursAction($id, Request $request){
		$repository = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Sejour');
		$listActivite = $repository->findOneById($id)->getActivites();
		
		$listDef = array("data"=>array());
		foreach($listActivite as $act)
		{
			$eve= array(
			"<center>".$act->getNom()."</center>",
			"<center>".$act->getCategorie()->getCategorie()."</center>",
			"<center>".$act->getCreateur()->getPN()."</center>",
			"<center>".$act->getNbAnim()."</center>",
			"<center>".$act->getNbEnfantMin()."</center>",
			"<center>".$act->getNbEnfantMax()."</center>",
			);
				
			array_push($listDef["data"], $eve);
		}
		
		$encoders = array(new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$response = new Response($serializer->serialize($listDef, 'json'));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listeEvenementAction($id, Request $req){
		$repository = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Sejour');
		$Sejour = $repository->findOneById($id);
		$repository2 = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Jour');
		$listeJour = $repository2->findBySejour($id);
		$repository3 = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('SejourBundle:Evenement');
		$listDef = array();				  
		foreach($listeJour as $jour)
		{
			$listEvenements = $repository3->findBy(array('jour' => $jour->getId()));
			foreach($listEvenements as $ev)
			{
				$eve= array(
							'title' => $ev->getActivite()->getNom(),
							'start' => $ev->getJour()->getDate()->format('Y-m-d')."T".$ev->getHeureDebut()->format('H:i'),
							'end' => $ev->getJour()->getDate()->format('Y-m-d')."T".$ev->getHeureFin()->format('H:i'),
							'color' => $ev->getActivite()->getCategorie()->getCouleur(),
							'id' => $ev->getId(),
							);
				array_push($listDef, $eve);
			}			
		}
		$encoders = array(new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$response = new Response($serializer->serialize($listDef, 'json'));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}	
}

