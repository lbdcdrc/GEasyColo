<?php

namespace UserBundle\Controller;

use UserBundle\Form\ImageType;
use SejourBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    public function photoAction(Request $request)
    {
		$user = $this->getUser();
		
		if ($user->getImage()==null)
		{
			$Image=new Image();
		}
		else
		{
			$Image=$user->getImage();
		}
		
		if (null === $user)
		{
			throw new NotFoundHttpException("Aucun utilisateur n'est connecté...");
		}
		
		
		$form = $this->get('form.factory')->create(ImageType::class, $Image);


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{
			$em = $this->getDoctrine()->getManager();
			$user->setImage($Image);
			$this->getDoctrine()->getManager()->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'avatar a bien été modifié');
			$URLImage = $user->getImage();

				$cacheManager = $this->get('liip_imagine.cache.manager');
				$cacheManager->remove($URLImage->getwebPath(), 'my_thumb');
				$cacheManager->remove($URLImage->getwebPath(), 'md_thumb');
				$cacheManager->remove($URLImage->getwebPath(), 'lg_thumb');
			
			$imagemanagerResponse = $this->container->get('liip_imagine.controller')->filterAction($request, $URLImage->getwebPath(), 'my_thumb');
			$imagemanagerResponse = $this->container->get('liip_imagine.controller')->filterAction($request, $URLImage->getwebPath(), 'md_thumb');
			$imagemanagerResponse = $this->container->get('liip_imagine.controller')->filterAction($request, $URLImage->getwebPath(), 'lg_thumb'); 
			
		  return $this->redirectToRoute('fos_user_profile_show');
		}		

		return $this->render('UserBundle:Default:editpicture.html.twig', array('form' => $form->createView(),));
    }
}
