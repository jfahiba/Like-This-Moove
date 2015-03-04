<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\Video;
use LTM\PortailBundle\Form\VideoForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class VideoController extends Controller
{


    public function editerAction($slug = null)
    {
        $message='';
        $em = $this->container->get('doctrine')->getManager();
        $context = $this->get('security.context');
		$usr = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

        if (isset($slug))
        {
            // modification d'une video existante : on recherche ses données
            $video =   $em->getRepository('PortailBundle:Video')
                ->findOneBy(array('slug' => $slug));

            if (!$video)
            {
                $message='Aucune video trouvée';
                $video = new Video();
                $video->setAuthor($usr);
                $video->setCreationDate(new \DateTime("now"));
            }else if ($video->getAuthor() != $usr and $usr->getId() != 50 and $usr->getId() != 54){
				throw new NotFoundHttpException("Element non trouvé");
			}
        }
        else
        {
            // ajout d'une nouvelle video
            $video = new Video();
            $video->setAuthor($usr);
            $video->setCreationDate(new \DateTime("now"));
        }



        $form = $this->container->get('form.factory')->create(new VideoForm(), $video);

        $request = $this->container->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $video->upload();
                $video->uploadImage();

                $em->persist($video);
                $em->flush();
                if (isset($slug))
                {
                    $message='Video modifiée avec succès !';
                }
                else
                {
                    $message='Video ajoutee avec succès !';
                    $actionManager = $this->get('spy_timeline.action_manager');
                    $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
                    $em->persist($subject);
                    $action        = $actionManager->create($subject,  'ajouter une video',
                        array('directComplement' => $video->getId(), 'payante' => false,
                        'slug' => $video->getSlug()) );

                    $actionManager->updateAction($action);

                }
            }
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:Video:editer.html.twig',
            array(
                'form' => $form->createView(),
                'message' => $message,
                'id' => $video->getId(),
                'slug' =>$video->getSlug()
            ));
    }


    public function listerAction($slug = null, $styled = null)
    {


        $post = Request::createFromGlobals();

        if ($post->request->has('submit')) {
            $request = $post->request;
            $pageNumber = 1;
        }else {
            $request = $this->get('request');
            $pageNumber = $this->get('request')->query->get('page', 1);
        }

        $username = $request->get('username');
        $title = $request->get('title');
        $creationDateInferior = $request->get('creationDateInferior');
        $creationDateSuperior = $request->get('creationDateSuperior');
		$tri = $request->get('tri');



        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('v')
            ->from('PortailBundle:Video', 'v');

        if ($slug != null) {
            $usr = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));
            $username = $usr->getUsername();
            $qb->andWhere(
                $qb->expr()->eq('v.author', '?1'))
                ->setParameter('1', $usr);

        } elseif ($username != null) {

            $qbc = $em->createQueryBuilder();
            $qbc->select('u')
                ->from('PortailBundle:User', 'u')
                ->where(
                    $qb->expr()->like('u.username', ':username') . ' OR '.
                    $qb->expr()->like('u.lastName', ':username') . ' OR '.
                    $qb->expr()->like('u.firstName', ':username') )
                ->setParameter('username', '%'.$username.'%');
            $authors = $qbc->getQuery()->getResult();

            $qb->andWhere(
                $qb->expr()->in('v.author', '?1'))
                ->setParameter('1', $authors);

        }

        if ($title != null) {
            $qb->andWhere(
                $qb->expr()->like('v.title', ':title') . ' OR '.
                $qb->expr()->like('v.description', ':title')  )
                ->setParameter('title', '%'.$title.'%');
        }


        if ($styled != null) {
            $styledanse = str_replace('_', ' ', $styled);
        } else {
            $styledanse = $request->get('styledanse');
        }

        if ($styledanse != null && $styledanse != "") {


            $styleVal = $em
                ->getRepository('PortailBundle:CDance')
                ->findOneBy(array('nom' => $styledanse));

            $qb->andWhere(':tag MEMBER OF v.style')
                ->setParameter('tag', $styleVal);
        }

        if ($creationDateSuperior != "") {

            $creationDateSup = $this->parseDate(date_parse_from_format('d/m/Y', $creationDateSuperior));

            $qb->andWhere('v.creationDate >= :date_from');
            $qb->setParameter('date_from', $creationDateSup, \Doctrine\DBAL\Types\Type::DATE);


        }
        if ($creationDateInferior != "") {

            $creationDateInf = $this->parseDate(date_parse_from_format('d/m/Y',$creationDateInferior));

            $qb->andWhere( 'v.creationDate <= :date_to');
            $qb->setParameter('date_to', $creationDateInf, \Doctrine\DBAL\Types\Type::DATE);

        }
		if ($tri == '2') {
			//$qb->orderBy('v.votes', 'DESC');
		}else if ($tri == '1') {
			$qb->orderBy('v.vues', 'DESC');
		} else {
			$qb->orderBy('v.creationDate', 'DESC');
		}
        
        $videos = $qb->getQuery()->getResult();

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $videos,
            $pageNumber/*page number*/,
            12/*limit per page*/
        );


        $allstyles = $em
            ->getRepository('PortailBundle:CDance')->findAll();


        return $this->container->get('templating')->renderResponse('PortailBundle:Video:lister.html.twig',
            array(
                'videos' => $videos,
                'pagination' => $pagination,
                'username' => (isset ($username)? $username : null),
                'title'  => (isset ($title)? $title : null),
                'creationDateInferior' => (isset ($creationDateInferior)? $creationDateInferior : null),
                'creationDateSuperior' => (isset ($creationDateSuperior)? $creationDateSuperior : null),
				'tri' => (isset ($tri)? $tri : '0'),
                'styledanse'  => (isset ($styledanse)? $styledanse : null),
                'slug' => $slug,
                'styled' => $styled,
                'allstyles' => $allstyles
            ));


    }


    private function parseDate($bDay) {

        $dateTime = new \DateTime('now');

        $dateTime->setDate($bDay["year"],
            $bDay["month"],
            $bDay["day"]);


        return $dateTime;
    }

    public function topAction($max = 5, $slug = null, $style = null )
    {
        $em = $this->container->get('doctrine')->getManager();

        if ($slug != null) {
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));

            $videos = $em->getRepository('PortailBundle:Video')->findBy(
                array('author' => $user),
                array('creationDate' => 'DESC'),
                $max,
                null
            );

        } else {
            $videos = $em->getRepository('PortailBundle:Video')->findBy(
                array(),
                array('creationDate' => 'DESC'),
                $max,
                null
            );
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $videos,
            $this->get('request')->query->get('page', 1)/*page number*/,
            30000/*limit per page*/
        );


        return $this->container->get('templating')->renderResponse(
            ($style == 'carousel'? 'PortailBundle:Video:listecarousel.html.twig':
                ($style == 'bx'? 'PortailBundle:Video:listebx.html.twig': 'PortailBundle:Video:liste.html.twig')),
            array(
                'videos' => $videos,
                'pagination' => $pagination,
                'tuto' => false
            ));
    }


    public function afficherAction()
    {
        $request = $this->container->get('request');


        $id = $request->get('id');

        $em = $this->container->get('doctrine')->getManager();
        $videoId = $this->get('nzo_url_encryptor')->decrypt($id);


        $video = $em->find('PortailBundle:Video', $videoId);
        if (!$video)
        {
            throw new NotFoundHttpException("Video non trouvée"); 
        } else  {
			
			$video->incrementVues($em);

            return $this->container->get('templating')->renderResponse('PortailBundle:Video:afficher.html.twig', array(
                'video' => $video,
                'user' => $video->getAuthor(),
                'tuto' => false));
        }

    }

    public function supprimerAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();

        $video =   $em->getRepository('PortailBundle:Video')
            ->findOneBy(array('slug' => $slug));


        if (!$video)
        {
            throw new NotFoundHttpException("Video non trouvée");
        }
        $slugUser = $video->getAuthor()->getSlug();


        $em->remove($video);
        $em->flush();


        return new RedirectResponse($this->container->get('router')->generate('ltm_video_filtrer',
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

        return $this->container->get('templating')->renderResponse('PortailBundle:Video:dropzone.html.twig', array(
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

        $videos= $em->getRepository('PortailBundle:Video')->findBy(
            array('author' => $user),array('creationDate' => 'DESC'));

        return $this->container->get('templating')->renderResponse('PortailBundle:Video:gallery.html.twig',
            array(
                'videos' => $videos, 'user' => $user
            ));
    }

}
