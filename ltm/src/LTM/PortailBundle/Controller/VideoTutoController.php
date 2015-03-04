<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\VideoTuto;
use LTM\PortailBundle\Form\VideoTutoForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;

class VideoTutoController extends Controller
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
            $video =   $em->getRepository('PortailBundle:VideoTuto')
                ->findOneBy(array('slug' => $slug));

            if (!$video)
            {
                $message='Aucune video trouvée';
                $video = new VideoTuto();
                $video->setCreationDate(new \DateTime("now"));

            } else if ($video->getAuthor() != $usr  and $usr->getId() != 50 and $usr->getId() != 54){
				throw new NotFoundHttpException("Element non trouvé");
			}
        }
        else
        {
            // ajout d'une nouvelle video
            $video = new VideoTuto();

        }

        $video->setAuthor($usr);

        $form = $this->container->get('form.factory')->create(new VideoTutoForm(), $video);

        $request = $this->container->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $video->upload();
                $video->uploadImage();
				$video->uploadExtrait();
				
                $em->persist($video);
                $em->flush();
                if (isset($id))
                {
                    $message='Video modifiée avec succès !';
                }
                else
                {
                    $message='Video ajoutée avec succès !';

                    $actionManager = $this->get('spy_timeline.action_manager');
                    $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
                    $em->persist($subject);
                    $action        = $actionManager->create($subject,  'ajouter une video',
                        array('directComplement' => $video->getId(),
                              'slug' => $video->getSlug(),
                            'payante' => true) );
                    $actionManager->updateAction($action);
                }
            } else{

            }
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:VideoTuto:editer.html.twig',
            array(
                'form' => $form->createView(),
                'message' => $message,
                'id' => $video->getId(),
                'slug' =>$video->getSlug(),
                'tuto' => true
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
            $credits = $request->get('credits');
            $creationDateInferior = $request->get('creationDateInferior');
            $creationDateSuperior = $request->get('creationDateSuperior');
		    $tri = $request->get('tri');


            $em = $this->container->get('doctrine')->getManager();

            $qb = $em->createQueryBuilder();
            $qb->select('v')
                ->from('PortailBundle:VideoTuto', 'v');

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
            if( $credits  != "") {
                $qb->andWhere('v.price <= :price');
                $qb->setParameter('price', $credits, \Doctrine\DBAL\Types\Type::FLOAT);

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

        return $this->container->get('templating')->renderResponse('PortailBundle:VideoTuto:lister.html.twig',
            array(
                'videos' => $videos,
                'pagination' => $pagination,
                'username' => (isset ($username)? $username : null),
                'title'  => (isset ($title)? $title : null),
                'credits'  => (isset ($credits)? $credits : null),
                'creationDateInferior' => (isset ($creationDateInferior)? $creationDateInferior : null),
                'creationDateSuperior' => (isset ($creationDateSuperior)? $creationDateSuperior : null),
                'styledanse'  => (isset ($styledanse)? $styledanse : null),
				'tri' => (isset ($tri)? $tri : '0'),
                'tuto' => true,
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
            $videos = $em->getRepository('PortailBundle:VideoTuto')->findBy(
                array('author' => $user),
                array('creationDate' => 'DESC'),
                $max,
                null
            );

        } else {
            $videos = $em->getRepository('PortailBundle:VideoTuto')->findBy(
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
            ($style == 'carousel'? 'PortailBundle:VideoTuto:listecarousel.html.twig':
                ($style == 'bx'? 'PortailBundle:VideoTuto:listebx.html.twig': 'PortailBundle:VideoTuto:liste.html.twig')),
            array(
            'videos' => $videos,
                'pagination' => $pagination,
                'tuto' => true
        ));
    }


    public function afficherAction()
    {
        $request = $this->container->get('request');


        $id = $request->get('id');

        $em = $this->container->get('doctrine')->getManager();
        $videoId = $this->get('nzo_url_encryptor')->decrypt($id);


        $video = $em->find('PortailBundle:VideoTuto', $videoId);
        if (!$video)
        {
            throw new NotFoundHttpException("Video non trouvée");
        } else {

            if ($video->getPrice() == 0) {

				$video->incrementVues($em);
				
                return $this->container->get('templating')->renderResponse('PortailBundle:VideoTuto:afficher.html.twig', array(
                    'video' => $video,
                    'user' => $video->getAuthor(),

                    'tuto' => true));
            }
            else {

				$context = $this->get('security.context');
				
                $token = $context->getToken();
                if ($token == null or $token->getUser() == null) {
                    // Veuillez vous connecter pour utiliser vos crédits
                    return new RedirectResponse($this->container->get('router')->generate('private'));
                }
				
                $currentUser =  $user = $em->find('PortailBundle:User', $token->getUser()->getId());
                if ($video->getAuthor()->getId() == $currentUser->getId() or $currentUser->hasBought($video)) {
					
					$video->incrementVues($em);
					
                    return $this->container->get('templating')->renderResponse('PortailBundle:VideoTuto:afficher.html.twig', array(
                        'video' => $video,
                        'user' => $video->getAuthor(),

                        'tuto' => true));

                } else if ($currentUser->buy($video, $em)) {
					
					$video->incrementVues($em);
					
                    $actionManager = $this->get('spy_timeline.action_manager');
                    $subject       = $actionManager->findOrCreateComponent('\User', $video->getAuthor()->getSlug());
                    $em->persist($subject);
                    $action        = $actionManager->create($subject,  'achete une video',
                        array('directComplement' => $video->getId(), 'payante' => true) );
                    $actionManager->updateAction($action);

                    return $this->container->get('templating')->renderResponse('PortailBundle:VideoTuto:afficher.html.twig', array(
                        'video' => $video,
                        'user' => $video->getAuthor(),
                        'tuto' => true));
                } else {
                    return new RedirectResponse($this->container->get('router')->generate('ltm_payment_ajouter', array(
                        'tuto' => true)));
                }

            }
        }

    }

	public function afficherExtraitAction()
    {
        $request = $this->container->get('request');


        $id = $request->get('id');

        $em = $this->container->get('doctrine')->getManager();
        $videoId = $this->get('nzo_url_encryptor')->decrypt($id);


        $video = $em->find('PortailBundle:VideoTuto', $videoId);
        if (!$video)
        {
            throw new NotFoundHttpException("Video non trouvée");
        } else {

           
                return $this->container->get('templating')->renderResponse('PortailBundle:VideoTuto:afficherextrait.html.twig', array(
                    'video' => $video,
                    'user' => $video->getAuthor(),
                    'tuto' => true));
        }

    }
	
    public function supprimerAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $video =  $em->getRepository('PortailBundle:VideoTuto')
            ->findOneBy(array('slug' => $slug));

        if (!$video)
        {
            throw new NotFoundHttpException("Video non trouvée");
        }
        $slugUser = $video->getAuthor()->getId();


        $em->remove($video);
        $em->flush();


        return new RedirectResponse($this->container->get('router')->generate('ltm_video_tuto_filtrer',
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

        
        return $this->container->get('templating')->renderResponse('PortailBundle:VideoTuto:dropzone.html.twig', array(
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

        $videos= $em->getRepository('PortailBundle:VideoTuto')->findBy(
                array('author' => $user),array('creationDate' => 'DESC'));

       
        return $this->container->get('templating')->renderResponse('PortailBundle:VideoTuto:gallery.html.twig',
            array(
                'videos' => $videos, 'user' => $user
            ));
    }


}
