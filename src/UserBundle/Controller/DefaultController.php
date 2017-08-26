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
			$this->getUser()->setImage($Image);
			$this->getDoctrine()->getManager()->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'avatar a bien été modifié');

			$cacheManager = $this->get('liip_imagine.cache.manager');
			$cacheManager->remove($this->getUser()->getImage()->getwebPath(), 'my_thumb');
			$cacheManager->remove($this->getUser()->getImage()->getwebPath(), 'md_thumb');
			$cacheManager->remove($this->getUser()->getImage()->getwebPath(), 'lg_thumb');
			$this->container->get('liip_imagine.controller')->filterAction($request, $this->getUser()->getImage()->getwebPath(), 'my_thumb');
			$this->container->get('liip_imagine.controller')->filterAction($request, $this->getUser()->getImage()->getwebPath(), 'md_thumb');
			$this->container->get('liip_imagine.controller')->filterAction($request, $this->getUser()->getImage()->getwebPath(), 'lg_thumb'); 
			
			return $this->redirectToRoute('fos_user_profile_show');
		}		

		return $this->render('UserBundle:Default:editpicture.html.twig', array('form' => $form->createView(),));
    }
}
