<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\Image;
use LTM\PortailBundle\Form\ImageForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class ImageController extends Controller
{


    public function editerAction($slug = null)
    {
        $message='';
        $em = $this->container->get('doctrine')->getManager();
        $context = $this->get('security.context');
		$usr = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());


        if (isset($slug))
        {
            // modification d'une image existante : on recherche ses données
            $image = $em->getRepository('PortailBundle:Image')
                ->findOneBy(array('slug' => $slug));
            if (!$image)
            {
                $message='Aucune image trouvée';
                $image = new Image();
                $image->setCreationDate(new \DateTime("now"));
                $image->setAuthor($usr);
            } else if ($image->getAuthor() != $usr and $usr->getId() != 50 and $usr->getId() != 54){
				throw new NotFoundHttpException("Element non trouvé");
			}
        }
        else
        {
            // ajout d'une nouvelle image
            $image = new Image();
            $image->setCreationDate(new \DateTime("now"));
            $image->setAuthor($usr);
        }



        $form = $this->container->get('form.factory')->create(new ImageForm(), $image);

        $request = $this->container->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $image->upload();

                $em->persist($image);
                $em->flush();
                if (isset($slug))
                {
                    $message='Image modifiée avec succès !';
                }
                else
                {
                    $message='Image ajoutée avec succès !';
                    $actionManager = $this->get('spy_timeline.action_manager');
                    $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
                    $em->persist($subject);
                    $action        = $actionManager->create($subject,  'ajouter une image',
                        array('directComplement' => $image->getAbsolutePath()   ) );
                    $actionManager->updateAction($action);

                }
            }
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:Image:editer.html.twig',
            array(
                'form' => $form->createView(),
                'message' => $message,
                'slug' => $image->getSlug()
            ));
    }


    public function listerAction($slug = null)
    {
        $em = $this->container->get('doctrine')->getManager();


        if ($slug != null) {

            $usr = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));
            $images= $em->getRepository('PortailBundle:Image')->findBy(array('author' => $usr));
        } else {
            $images= $em->getRepository('PortailBundle:Image')->findAll();
        }
        return $this->container->get('templating')->renderResponse('PortailBundle:Image:lister.html.twig',
            array(
                'images' => $images, 'tuto' => false
            ));

    }



    public function topAction($max = 5, $slug = null)
    {
        $em = $this->container->get('doctrine')->getManager();


        if ($slug != null) {
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));

            $images = $em->getRepository('PortailBundle:Image')->findBy(
                array('author' => $user),
                array('creationDate' => 'DESC'),
                $max,
                null
            );

        } else {
            $images = $em->getRepository('PortailBundle:Image')->findBy(
                array('creationDate' => 'DESC'),
                $max,
                null
            );
        }
        return $this->container->get('templating')->renderResponse('PortailBundle:Image:liste.html.twig', array(
            'images' => $images,
        ));
    }

    public function listecarouselAction($max = 4, $slug = null)
    {
        $em = $this->container->get('doctrine')->getManager();


        if ($slug != null) {
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));

            $images = $em->getRepository('PortailBundle:Image')->findBy(
                array('author' => $user),
                array('creationDate' => 'DESC'),
                $max,
                null
            );
        } else {
            $images = $em->getRepository('PortailBundle:Image')->findBy(
                array(),
                array('creationDate' => 'DESC'),
                $max,
                null
            );
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:Image:listecarousel.html.twig', array(
            'images' => $images,
        ));
    }


    public function afficherAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $image = $em->getRepository('PortailBundle:Image')
            ->findOneBy(array('slug' => $slug));

        if (!$image)
        {
            throw new NotFoundHttpException("Image non trouvée");
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:Image:afficher.html.twig', array(
            'image' => $image,
        ));


    }

    public function supprimerAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $image = $em->getRepository('PortailBundle:Image')
            ->findOneBy(array('slug' => $slug));

        if (!$image)
        {
            throw new NotFoundHttpException("Image non trouvée");
        }

        $em->remove($image);
        $em->flush();

        $slugUser = $image->getAuthor()->getSlug();
        return new RedirectResponse($this->container->get('router')->generate('ltm_image_filtrer',
            array('slug' => $slugUser)));
    }


    public function dropzoneAction(){

        $em = $this->container->get('doctrine')->getManager();
        $context = $this->get('security.context');
        $user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());


        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:Image:dropzone.html.twig', array(
            'user' => $user));
    }

    public function galleryAction(){

        $em = $this->container->get('doctrine')->getManager();

        $context = $this->get('security.context');
        $user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());


        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        $images= $em->getRepository('PortailBundle:Image')->findBy(
            array('author' => $user),array('creationDate' => 'DESC'));


        return $this->container->get('templating')->renderResponse('PortailBundle:Image:gallery.html.twig',
            array(
                'images' => $images, 'user' => $user
            ));
    }
}
