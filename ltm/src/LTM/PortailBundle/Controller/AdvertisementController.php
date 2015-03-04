<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\Advertisement;
use LTM\PortailBundle\Form\AdvertisementForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class AdvertisementController extends Controller
{

	public function editerproAction($slug = null) {
		return $this->editerAction($slug, true);
	}
    public function editerAction($slug = null, $montrerProUntil = false)
    {
        $message='';
        $em = $this->container->get('doctrine')->getManager();
        $usr= $this->get('security.context')->getToken()->getUser();


        if (isset($slug))
        {
            // modification d'une advertisement existante : on recherche ses données
            $advertisement = $em->getRepository('PortailBundle:Advertisement')
                ->findOneBy(array('slug' => $slug));

            if (!$advertisement)
            {
                $message='Aucune annonce trouvée';
                $advertisement = new Advertisement();
                $advertisement->setCreationDate(new \DateTime("now"));
                $advertisement->setAuthor($usr);
            }else if ($advertisement->getAuthor() != $usr and $usr->getId() != 50 and $usr->getId() != 54){
                throw new NotFoundHttpException("Element non trouvé");
            }
        }
        else
        {
            // ajout d'une nouvelle advertisement
            $advertisement = new Advertisement();
            $advertisement->setCreationDate(new \DateTime("now"));
            $advertisement->setAuthor($usr);
        }



        $form = $this->container->get('form.factory')->create(new AdvertisementForm(), $advertisement);

        $request = $this->container->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {

                $now = new \DateTime("now");
                $price = round ((($advertisement->getProUntil() != null?
                            $advertisement->getProUntil()->getTimestamp() : 0 ) - $now->getTimestamp())*35/(60*60*24));

                $annoncePayante = $price > 35;
                if ($annoncePayante) {
                    if ($usr->buyAnnonce($advertisement, $price, $em)) {
                        $advertisement->upload();
                        $em->persist($advertisement);
                        $em->flush();
                    } else {
                        return new RedirectResponse($this->container->get('router')->generate('ltm_payment_ajouter', array(
                            'tuto' => false)));
                    }
                } else {
                    $advertisement->setProUntil(null);
                    $advertisement->upload();
                    $em->persist($advertisement);
                    $em->flush();
                }

                if (isset($slug))
                {
                    $message='Annonce modifiée avec succès !';
                }
                else
                {

                    $message='Annonce ajoutée avec succès !';
                    $actionManager = $this->get('spy_timeline.action_manager');
                    $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
                    $em->persist($subject);
                    $action        = $actionManager->create($subject,  'ajouter une annonce',
                        array(
                            'directComplement' => ($advertisement->getAbsolutePath() == null? '' : $advertisement->getAbsolutePath()),
                            'slug' => $advertisement->getSlug()
                        ) );
                    $actionManager->updateAction($action);

                }
            }
        }

        return $this->container->get('templating')->renderResponse(
            ($montrerProUntil? 'PortailBundle:Advertisement:editerpro.html.twig': 'PortailBundle:Advertisement:editer.html.twig'),
            array(
                'form' => $form->createView(),
                'message' => $message,
                'slug' => $advertisement->getSlug()
            ));
    }

	

    public function listerProAction($slug = null)
    {
        $em = $this->container->get('doctrine')->getManager();

        if ($slug != null) {
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));
            $advertisements= $em->getRepository('PortailBundle:Advertisement')->findBy(array('author' => $user));
        } else {
            
			$qb = $em->createQueryBuilder();
			$qb->select('a')
            ->from('PortailBundle:Advertisement', 'a') ;
			
			$now = new \DateTime("now");
			
			$qb->andWhere($qb->expr()->isNotNull('a.proUntil'));
            $qb->andWhere('a.proUntil >= :date_from');
            $qb->setParameter('date_from', $now, \Doctrine\DBAL\Types\Type::DATE);
			
			$advertisements = $qb->getQuery()->getResult();
        }
		
        return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:lister.html.twig',
            array(
                'advertisements' => $advertisements, 
				'tuto' => false,
				'alladvertisements' => null
            ));
    }
	
	public function listerToutesAction($slug = null)
    {
        $em = $this->container->get('doctrine')->getManager();


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
		$tri = $request->get('tri');
		$place = $request->get('place');
			
		$qba = $em->createQueryBuilder();
			$qba->select('a')
            ->from('PortailBundle:Advertisement', 'a') ;
		
		if ($slug != null) {
            $usr = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));
            $username = $usr->getUsername();
            $qba->andWhere(
                $qba->expr()->eq('a.author', '?1'))
                ->setParameter('1', $usr);

        } elseif ($username != null) {

            $qbc = $em->createQueryBuilder();
            $qbc->select('u')
                ->from('PortailBundle:User', 'u')
                ->where(
                    $qbc->expr()->like('u.username', ':username') . ' OR '.
                    $qbc->expr()->like('u.lastName', ':username') . ' OR '.
                    $qbc->expr()->like('u.firstName', ':username') )
                ->setParameter('username', '%'.$username.'%');
            $authors = $qbc->getQuery()->getResult();

            $qba->andWhere(
                $qba->expr()->in('a.author', '?1'))
                ->setParameter('1', $authors);
        }
		
		if ($title != null) {
                $qba->andWhere(
                    $qba->expr()->like('a.title', ':title') . ' OR '.
                    $qba->expr()->like('a.description', ':title')  )
                    ->setParameter('title', '%'.$title.'%');
            }
		
		 if ($tri == '2') {
			//$qb->orderBy('v.votes', 'DESC');
			}else if ($tri == '1') {
				//$qb->orderBy('a.vues', 'DESC');
			} else {
				$qba->orderBy('a.creationDate', 'DESC');
			}
			
		if ($place != null) {
            
			$qba->andWhere(
                    $qba->expr()->like('a.city', ':place') . ' OR '.
                    $qba->expr()->like('a.country', ':place') )
                ->setParameter('place', '%'.$place.'%');
				 
        }
		
		$context = $this->get('security.context');
		if ($context->getToken()->getUser() == 'anon.') {
			 $qba->setMaxResults(14);
		}
		
		
		$alladvertisements = $qba->getQuery()->getResult();
			
		if ($context->getToken()->getUser() == 'anon.') {
			 $qba->setMaxResults(14);
		}
		 
		$paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $alladvertisements,
            $pageNumber/*page number*/,
            14/*limit per page*/
        );
        return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:listerlines.html.twig',
            array(
                'advertisements' => null, 
				'tuto' => false,
				'slug' => $slug,
				'pagination' => $pagination,
				'username' => (isset ($username)? $username : null),
				'tri' => (isset ($tri)? $tri : null),
				'place' => (isset ($place)? $place : null),
				'title' => (isset ($title)? $title : null),
				'countAnnonces' =>count($alladvertisements)
            ));
    }
 
    public function topAction($max = 5, $slug = null)
    {
        $em = $this->container->get('doctrine')->getManager();


        if ($slug != null) { // Gallerie
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));
			
			$qb = $em->createQueryBuilder();
			$qb->select('a')
            ->from('PortailBundle:Advertisement', 'a') ;
			
			/*$now = new \DateTime("now");
            $qb->andWhere($qb->expr()->isNotNull('a.proUntil'));
			$qb->andWhere('a.proUntil >= :date_from');
            $qb->setParameter('date_from', $now, \Doctrine\DBAL\Types\Type::DATE);*/
			$qb->andWhere('a.author = :author');
			$qb->setParameter('author', $user );
			$qb->orderBy('a.creationDate', 'DESC');
			$qb->setMaxResults($max);
			$advertisements = $qb->getQuery()->getResult();
			 
			return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:liste.html.twig', array(
            'alladvertisements' => $advertisements, 'advertisements' => '', 
			'countAnnonces' =>count($advertisements)
        ));
        } else { // Footer
            $qb = $em->createQueryBuilder();
			$qb->select('a')
            ->from('PortailBundle:Advertisement', 'a') ;
			
			$now = new \DateTime("now");
			$qb->andWhere($qb->expr()->isNotNull('a.proUntil'));
            $qb->andWhere('a.proUntil >= :date_from');
            $qb->setParameter('date_from', $now, \Doctrine\DBAL\Types\Type::DATE);
			$qb->orderBy('a.creationDate', 'DESC');
			$qb->setMaxResults($max);
			$advertisements = $qb->getQuery()->getResult();
			 return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:footer.html.twig', array(
            'advertisements' => $advertisements
        ));
        }
        
    }

    public function listecarouselAction($max = 500, $slug = null)
    {   // Pro
        $em = $this->container->get('doctrine')->getManager();


        if ($slug != null) {
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));
			
			$qb = $em->createQueryBuilder();
			$qb->select('a')
            ->from('PortailBundle:Advertisement', 'a') ;
			
			$now = new \DateTime("now");
            $qb->andWhere('a.proUntil >= :date_from');
            $qb->setParameter('date_from', $now, \Doctrine\DBAL\Types\Type::DATE);
			$qb->andWhere('a.author == :author');
			$qb->setParameter('author', $user );
			$qb->orderBy('a.creationDate', 'DESC');
			$qb->setMaxResults($max);
			$advertisements = $qb->getQuery()->getResult();
        } else {
            $qb = $em->createQueryBuilder();
			$qb->select('a')
            ->from('PortailBundle:Advertisement', 'a') ;
			
			$now = new \DateTime("now");
            $qb->andWhere('a.proUntil >= :date_from');
            $qb->setParameter('date_from', $now, \Doctrine\DBAL\Types\Type::DATE); 
			$qb->orderBy('a.creationDate', 'DESC');
			$qb->setMaxResults($max);
			$advertisements = $qb->getQuery()->getResult();
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:listecarousel.html.twig', array(
            'advertisements' => $advertisements,
        ));
    }


    public function afficherAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();

        if (!$slug)
        {
            throw new NotFoundHttpException("Annonce non trouvée");
        }

        $advertisement= $em->getRepository('PortailBundle:Advertisement')->findOneBy(array('slug' => $slug));

        if (!$advertisement)
        {
            throw new NotFoundHttpException("Annonce non trouvée");
        }

        $advertisement->incrementVues($em);

        return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:afficher.html.twig', array(
            'advertisement' => $advertisement,
            'tuto' => false,
            'user' => $advertisement->getAuthor()
        ));


    }

    public function supprimerAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        if (!$slug)
        {
            throw new NotFoundHttpException("Annonce non trouvée");
        }
        $advertisement= $em->getRepository('PortailBundle:Advertisement')->findOneBy(array('slug' => $slug));


        if (!$advertisement)
        {
            throw new NotFoundHttpException("Annonce non trouvée");
        }

        $em->remove($advertisement);
        $em->flush();

        $slugUser = $advertisement->getAuthor()->getSlug();
        return new RedirectResponse($this->container->get('router')->generate('ltm_advertisement_filtrer',
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

        

        return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:dropzone.html.twig', array(
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

        $advertisements= $em->getRepository('PortailBundle:Advertisement')->findBy(
            array('author' => $user),array('creationDate' => 'DESC'));

      

        return $this->container->get('templating')->renderResponse('PortailBundle:Advertisement:gallery.html.twig',
            array(
                'advertisements' => $advertisements, 'user' => $user
            ));
    }
}
