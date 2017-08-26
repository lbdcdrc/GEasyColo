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

class DefaultController extends Controller
{
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function indexAdminAction()
    {
		return $this->render('AdminBundle:Default:indexadmin.html.twig');
    }
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	private function datatable() {
		return $this->get('datatable')
					->setEntity("UserBundle:User", "x")                         
					->setFields(
								array(
									"Nom d'utilisateur" => 'x.username',
									"État civil"	=> 'x.nom',
									"Coordonnées" => 'x.email',
									"Qualifications" => 'd.nom',
									"Actions" => 'x.id',
									"_identifier_" => 'x.id')
						)
						->addJoin('x.diplome', 'd', \Doctrine\ORM\Query\Expr\Join::INNER_JOIN)
						->setRenderers(
								array(
									1 => array('view' => 'AdminBundle:TableUtilisateurs:etatcivil.html.twig',),
									2 => array('view' => 'AdminBundle:TableUtilisateurs:coordonnees.html.twig',),
									3 => array('view' => 'AdminBundle:TableUtilisateurs:qualifications.html.twig',),
									4 => array('view' => 'AdminBundle:TableUtilisateurs:roles.html.twig',),
								)
						)
					->setGlobalSearch(true);                        
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function gridAction()
	{
		return $this->datatable()->execute();                                      
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function indexAction(Request $request)
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
		$this->datatable();
		return $this->render('AdminBundle:Module:index.html.twig');
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
		
		
		return $this->render('AdminBundle:Default:affecterroles.html.twig', array('form' => $form->createView(), 'user'=>$user));
	}
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function profileAction($id, Request $request)
	{
		$userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
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
		
		
		return $this->render('AdminBundle:Profile:show.html.twig', array( 'user'=>$user, 'form'=>$form->createView()));
	}
}


