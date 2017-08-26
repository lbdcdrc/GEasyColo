<?php

namespace UserBundle\Controller;

use UserBundle\Form\Type\ImageType;
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
		$Image=$this->getUser()->getImage();
		
		if ($this->getUser()->getImage()===null)
		{
			$Image=new Image();
		}
		
		$form = $this->get('form.factory')->create(ImageType::class, $Image);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{
			$user->setImage($Image);
			$this->getDoctrine()->getManager()->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'avatar a bien été modifié');

			$cacheManager = $this->get('liip_imagine.cache.manager');
			$cacheManager->remove($user->getImage()->getwebPath(), 'my_thumb');
			$cacheManager->remove($user->getImage()->getwebPath(), 'md_thumb');
			$cacheManager->remove($user->getImage()->getwebPath(), 'lg_thumb');
			
			return $this->redirectToRoute('fos_user_profile_show');
		}		

		return $this->render('UserBundle:Default:editpicture.html.twig', array('form' => $form->createView(),));
    }
}
