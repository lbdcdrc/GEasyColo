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

class DefaultController extends Controller
{
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function indexAdminAction()
    {
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour');
		  
		$NbSejours = $repository->createQueryBuilder('a')
		 ->select('COUNT(a)')
		 ->getQuery()
		 ->getSingleScalarResult();
		$repository2 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('UserBundle:User');
		$NbUsers = $repository2->createQueryBuilder('a')
		 ->select('COUNT(a)')
		 ->getQuery()
		 ->getSingleScalarResult();
		$repository3 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Enfant');
		$NbEnfants = $repository3->createQueryBuilder('a')
		 ->select('COUNT(a)')
		 ->getQuery()
		 ->getSingleScalarResult();
		$repository4 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Activite');		
		$NbActivites = $repository4->createQueryBuilder('a')
		 ->select('COUNT(a)')
		 ->getQuery()
		 ->getSingleScalarResult();	
		$Disk = $this->Disk();
		return $this->render('AdminBundle:Default:indexadmin.html.twig', array('Disk'=> $Disk, 'NbSejours'=>$NbSejours, 'NbEnfants'=>$NbEnfants, 'NbUsers'=>$NbUsers, 'NbActivites'=>$NbActivites));
    }
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function usersAction(Request $request)
	{
		if ($request->isMethod('POST')) {
			$userManager = $this->get('fos_user.user_manager');
			$dataform = $request->request->get('userbundle_user');
			$user = $userManager->findUserBy(['id' => $dataform['id']]);
			if (null === $user) 
			{
				throw new NotFoundHttpException("L'utilisateur ".$id." n'existe pas.");
			}
			$user->setRoles($dataform['roles']);
			$userManager->updateUser($user);
			$request->getSession()->getFlashBag()->add('notice', 'Les rôles ont étés modifiés.');
			return $this->redirectToRoute('user_list');
		}
		$Disk = $this->Disk();
		return $this->render('AdminBundle:Utilisateurs:users.html.twig', array('Disk'=> $Disk,));
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function sejoursAction(Request $request)
	{
		$Disk = $this->Disk();
		return $this->render('AdminBundle:Sejours:sejours.html.twig', array('Disk'=> $Disk,));
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function enfantsAction(Request $request)
	{
		$Disk = $this->Disk();
		return $this->render('AdminBundle:Enfants:enfants.html.twig', array('Disk'=> $Disk,));
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function activitesAction(Request $request)
	{
		$Disk = $this->Disk();
		return $this->render('AdminBundle:Activites:activites.html.twig', array('Disk'=> $Disk,));
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function rolesAction($id, Request $request)
	{
		$roles_liste= $this->getParameter('security.role_hierarchy.roles');
		$roles=array();
		$roles[]=array('ANIMATEUR'=>'ROLE_USER');
		foreach ($roles_liste as $key => $val) 
		{
 			$roleDisplay = str_replace('ROLE_','', $key);
			$roleDisplay = str_replace('_',' ', $roleDisplay);
			$roles[]=array($roleDisplay=>$key);
		}
		
		$userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
		
		if (null === $user) 
		{
			throw new NotFoundHttpException("L'utilisateur ".$id." n'existe pas.");
		}
		
		$form   = $this->get('form.factory')->create(RolesType::class, $user, ['roles' => $roles, 'id'=>$user->getId()]);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
		  $userManager->updateUser($user);
		  $request->getSession()->getFlashBag()->add('notice', 'Les rôles ont étés modifiés.');
		  return $this->redirectToRoute('user_list');
		}
		
		$Disk = $this->Disk();
		return $this->render('AdminBundle:Default:affecterroles.html.twig', array('Disk'=> $Disk, 'form' => $form->createView(), 'user'=>$user));
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function profileAction($id, Request $request)
	{
		$userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour');
		$listDir = $repository->findBy(array('directeur'=>$user));
		$repository2 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:AnimSejour');	
		$listSej = $repository2->findBy(array('user'=>$user));
		$listSejour = array();
		foreach($listSej as $sej)
		{
			array_push($listSejour, $sej->getSejour());
		}
		if (null === $user) 
		{
			throw new NotFoundHttpException("L'utilisateur ".$id." n'existe pas.");
		}
		$form   = $this->get('form.factory')->create(ProfileType::class, $user, array('attr' => array('edit' => true)));
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
		  $userManager->updateUser($user);
		  $request->getSession()->getFlashBag()->add('notice', 'Le profil a été mis à jours.');
		  return $this->redirectToRoute('voir_profil', array('id' => $id));
		}
		$Disk = $this->Disk();
		return $this->render('AdminBundle:Profile:show.html.twig', array('Disk'=> $Disk, 'user'=>$user, 'listDir'=>$listDir, 'listSejour'=>$listSejour, 'form'=>$form->createView()));
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function voirSejoursAction($id, Request $request)
	{
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Sejour');
		$repository2 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:AnimSejour');
		$repository3 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Enfant');
		$repository4 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:Jour');
		$repository5 = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('SejourBundle:ForumCategorie');
		$NbEnfants = $repository3->createQueryBuilder('a')
		 ->select('COUNT(a)')
		 ->where('a.sejour = ?1')
		 ->setParameter(1, $id)
		 ->getQuery()
		 ->getSingleScalarResult();
		$Sejour = $repository->findOneById($id);
		$NbActi = count($Sejour->getActivites());
		$NbJours = sizeof($repository4->findBySejour($id));
		$ListeForum = $repository5->findBySejour($id);
		
		$repository6 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:AnimSejour');

		$repository7 = $this->getDoctrine()
		->getManager()
		->getRepository('SejourBundle:AnimConges');
		
		$listeJour = $repository4->findBy(
		array('sejour' => $id), // Critere
		array('date' => 'asc'));

		$listeAnim = $repository6->findBy(
		array('sejour' => $id), // Critere
		array('role' => 'asc'));

		$Directeur=$Sejour->getDirecteur();

		$listeAnimConges=array();
			
		$listeJ=array();
		foreach($listeJour as $Jour){
			$CongesAnim = $repository7->findOneBy(array('jour' => $Jour->getId(), 'user'=> $Directeur->getId()))->getMoment();
			$listeJ[$Jour->getId()]=$CongesAnim;
		}
		$JourDir=array();
		$JourDir[$Directeur->getId()]= $listeJ;
		
				
		foreach($listeAnim as $Anim){
			$listeJ=array();
			foreach($listeJour as $Jour){
				$CongesAnim = $repository7->findOneBy(array('jour' => $Jour->getId(), 'user'=> $Anim->getUser()->getId()))->getMoment();
				$listeJ[$Jour->getId()]=$CongesAnim;
			}
			$listeAnimConges[$Anim->getUser()->getId()]= $listeJ;
		}
		
		
		
		if (null === $Sejour) 
		{
			throw new NotFoundHttpException("Le séjour ".$id." n'existe pas.");
		}
		$Equipe = array();
		array_push($Equipe, array($Sejour->getDirecteur(), 'Directeur'));
		$Anim = $repository2->findBy(
             array('sejour'=> $Sejour), 
             array('role' => 'DESC')
           );
		foreach($Anim as $An)
		{
			if($An->getRole()===1)
			{
				array_push($Equipe, array($An->getUser(), 'Animateur'));
			}
			elseif($An->getRole()===2)
			{
				array_push($Equipe, array($An->getUser(), 'Assistant Sanitaire'));
			}		
			else
			{
				array_push($Equipe, array($An->getUser(), 'Adjoint'));
			}	
		}
		$Disk = $this->Disk();		
		return $this->render('AdminBundle:Sejours:showSejours.html.twig', array('Disk'=> $Disk, 
																				'Sejour'=>$Sejour,
																				'Equipe'=>$Equipe, 
																				'NbEnfants'=>$NbEnfants, 
																				'NbActi'=>$NbActi, 
																				'NbJours'=>$NbJours, 
																				'Forum'=>$ListeForum,
																				'listeJours'=> $listeJour,
																				'Directeur'=>$Directeur, 
																				'jourDir'=> $JourDir, 
																				'listeconges'=> $listeAnimConges,
																				'listeAnim'=>$listeAnim,));
	}
	private function Disk()
	{
		$DiskTotal = disk_total_space("/");
		$DiskFree = disk_free_space("/");
		$DiskUsed = $DiskTotal - $DiskFree;
		$DiskUsage = round((( $DiskTotal - $DiskFree ) / $DiskTotal ) * 100);
		$DiskUsed = $this->SpaceConversion($DiskUsed);
		$DiskTotal = $this->SpaceConversion($DiskTotal);
		return array($DiskTotal, $DiskUsed, $DiskUsage);
	}
	private function SpaceConversion($DiskSpace)
	{
		$types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		for( $i = 0; $DiskSpace >= 1024 && $i < ( count( $types ) -1 ); $DiskSpace /= 1024, $i++ );
		return( round( $DiskSpace, 2 ) . " " . $types[$i] );
	}
}
