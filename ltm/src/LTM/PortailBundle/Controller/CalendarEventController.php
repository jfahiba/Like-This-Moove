<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\CalendarEvent;
use LTM\PortailBundle\Form\CalendarEventForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class CalendarEventController extends Controller
{



    public function editerAction($slug = null)
    {
        $message='';
        $em = $this->container->get('doctrine')->getManager();
		
		$context = $this->get('security.context');
		$usr = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());
		
        if (isset($slug))
        {
            // modification d'un evenement existant : on recherche ses données
            $calendarEvent = $em->getRepository('PortailBundle:CalendarEvent')
                ->findOneBy(array('slug' => $slug));

            if (!$calendarEvent)
            {
                $message='Aucun horaire de calendrier trouvé';
                $calendarEvent = new CalendarEvent(); 
                $calendarEvent->setAuthor($usr);
                $calendarEvent->addParticipant($usr);
            } else if ($calendarEvent->getAuthor() != $usr and $usr->getId() != 50 and $usr->getId() != 54){
				throw new NotFoundHttpException("Element non trouvé");
			}
        }
        else
        {
            // ajout d'un nouvel evenement
            $calendarEvent = new CalendarEvent();
            $calendarEvent->setCreationDate(new \DateTime("now")); 
            $calendarEvent->setAuthor($usr);
            $calendarEvent->addParticipant($usr);

        }

        $form = $this->container->get('form.factory')->create(new CalendarEventForm(), $calendarEvent);

        $request = $this->container->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $calendarEvent->upload();
                $em->persist($calendarEvent);
                $em->flush();
                if (isset($slug))
                {
                    $message='Horaire modifié avec succès !';
                }
                else
                {
                    $message='Horaire ajouté avec succès !';

                    $actionManager = $this->get('spy_timeline.action_manager');
                    $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
                    $em->persist($subject);
                    $action        = $actionManager->create($subject,  'a cree un evenement',
                        array(
                            'directComplement' => ($calendarEvent->getAbsolutePath() == null ? '' : $calendarEvent->getAbsolutePath()),
                            'slug' => $calendarEvent->getSlug() ));

                    $actionManager->updateAction($action);


                }
            }
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:CalendarEvent:editer.html.twig',
            array(
                'form' => $form->createView(),
                'message' => $message,
                'slug' => $calendarEvent->getSlug(),
            ));
    }


    public function calendarAction()
    {
        $em = $this->container->get('doctrine')->getManager();



        $context = $this->get('security.context');
		$user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());


        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:CalendarEvent:calendar.html.twig',
            array( 'user' => $user ));

    }

    public function listerAction($slug = null, $styled = null, $categoryev = null)
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

        $endDateInferior = $request->get('endDateInferior');
        $startDateSuperior = $request->get('startDateSuperior');
		$place = $request->get('place');


        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('v')
            ->from('PortailBundle:CalendarEvent', 'v');

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

		if ($place != null) {
            $qb->andWhere(
			    $qb->expr()->like('v.location', ':place') . ' OR '.
                $qb->expr()->like('v.city', ':place') . ' OR '.
                $qb->expr()->like('v.country', ':place'))
                ->setParameter('place', '%'.$place.'%');
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

        if ($startDateSuperior != "") {

            $startDateSup = $this->parseDate(date_parse_from_format('d/m/Y', $startDateSuperior));

            $qb->andWhere('v.start >= :date_from OR v.end >= :date_from');
            $qb->setParameter('date_from', $startDateSup, \Doctrine\DBAL\Types\Type::DATE);


        } else {
            $qb->andWhere('v.start >= :date_from  OR v.end >= :date_from');
			$now = new \DateTime('now');
			$now->modify( '-1 day' );
            $qb->setParameter('date_from', $now, \Doctrine\DBAL\Types\Type::DATE);
			$startDateSuperior = $now->format('d/m/Y');
		}
        if ($endDateInferior != "") {

            $endDateInf = $this->parseDate(date_parse_from_format('d/m/Y',$endDateInferior));

            $qb->andWhere( 'v.end <= :date_to');
            $qb->setParameter('date_to', $endDateInf, \Doctrine\DBAL\Types\Type::DATE);

        }

        if ($categoryev != null) {
            $categoryevent = str_replace('_', ' ', $categoryev);
        } else {
            $categoryevent = $request->get('categoryevent');
        }

        if($categoryevent != null &&  $categoryevent  != "") {
            $catEvent = $em
                ->getRepository('PortailBundle:CCalendarEvent')
                ->findOneBy(array('nom' => $categoryevent));

            $qb->andWhere(':tag = v.category')
                ->setParameter('tag', $catEvent);

        }
        $qb->orderBy('v.start', 'ASC');
        $calendarevents = $qb->getQuery()->getResult();

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $calendarevents,
            $pageNumber/*page number*/,
            16/*limit per page*/
        );


        $allstyles = $em
            ->getRepository('PortailBundle:CDance')->findAll();

        $allcategoryevents = $em
            ->getRepository('PortailBundle:CCalendarEvent')->findAll();

        return $this->container->get('templating')->renderResponse('PortailBundle:CalendarEvent:lister.html.twig',
            array(
                'calendarevents' => $calendarevents,
                'pagination' => $pagination,
                'username' => (isset ($username)? $username : null),
                'title'  => (isset ($title)? $title : null),
                'categoryevent'  => (isset ($categoryevent)? $categoryevent : null),
                'endDateInferior' => (isset ($endDateInferior)? $endDateInferior : null),
                'startDateSuperior' => (isset ($startDateSuperior)? $startDateSuperior: null),
                'styledanse'  => (isset ($styledanse)? $styledanse : null),
				'place' => (isset ($place)? $place : null),
                'tuto' => true,
                'slug' => $slug,
                'styled' => $styled,
                'categoryev' => $categoryev,
                'allstyles' => $allstyles,
                'allcategoryevents' => $allcategoryevents
            ));



    }


    private function parseDate($bDay) {

        $dateTime = new \DateTime('now');

        $dateTime->setDate($bDay["year"],
            $bDay["month"],
            $bDay["day"]);


        return $dateTime;
    }

    public function topAction($max = 5, $idEvent = null, $slug=null, $idCategory=null)
    {
        $em = $this->container->get('doctrine')->getManager();

        $category = null;
        $user = null;
        if ($idCategory != null) {
            $category = $em->find('PortailBundle:CPublish', $idCategory);
        }

        $qb = $em->getRepository('PortailBundle:CalendarEvent')->createQueryBuilder('v');

        if ($slug != null)    {
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));
        }

        if ($category != null) {
            $qb->andWhere( $qb->expr()->eq('v.category', '?1'))
                ->setParameter('1', $category);
        }
        if ($user != null) {
            $qb->andWhere( $qb->expr()->eq('v.author', '?2'))
                ->setParameter('2', $user);
        }
        if ($idEvent != null) {
            $qb->andWhere( $qb->expr()->neq('v.id', '?3'))
                ->setParameter('3', $idEvent);
        }
		$qb->andWhere('v.start >= :date_from');
		$now = new \DateTime('now');
		$now->modify( '-1 day' );
		$qb->setParameter('date_from', $now, \Doctrine\DBAL\Types\Type::DATE);
			
        $qb->orderBy('v.start', 'ASC');
        $qb->setMaxResults($max);
        $calendarEvents = $qb->getQuery()->getResult();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $calendarEvents,
            $this->get('request')->query->get('page', 1)/*page number*/,
            30000/*limit per page*/
        );


        return $this->container->get('templating')->renderResponse('PortailBundle:CalendarEvent:listeportfolio.html.twig', array(
            'pagination' => $pagination,
            'calendarEvents' => $calendarEvents
        ));
    }



    public function afficherAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $calendarEvent = $em->getRepository('PortailBundle:CalendarEvent')
            ->findOneBy(array('slug' => $slug));

        if (!$calendarEvent)
        {
            throw new NotFoundHttpException("Horaire non trouvé");
        }


		$calendarEvent->incrementVues($em);
		
        return $this->container->get('templating')->renderResponse('PortailBundle:CalendarEvent:afficher.html.twig', array(
            'event' => $calendarEvent,
            'tuto' => false,
            'user' => $calendarEvent->getAuthor()
        ));


    }

    public function participantAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();

        $calendarEvent = $em->getRepository('PortailBundle:CalendarEvent')
            ->findOneBy(array('slug' => $slug));

        if (!$calendarEvent)
        {
            throw new NotFoundHttpException("Horaire non trouvé");
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:User:listePro.html.twig',
            array(
                'users' => $calendarEvent->getParticipant(),
                'relationTitle' => 'Participants',
                'relationClass' => '',
                'tuto' => false
            ));
    }
    public function participerAction($slug)
    {

        $em = $this->container->get('doctrine')->getManager();

        $calendarEvent = $em->getRepository('PortailBundle:CalendarEvent')
            ->findOneBy(array('slug' => $slug));


        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId()); ;

        // Si participant exite deja ou si null, ne rien faire

        if ($calendarEvent == null or $calendarEvent->getParticipant()->contains($me)) {
            $request = $this->container->get('request');
            $referer = $request->headers->get('referer');
            if (!$referer){
                return new RedirectResponse($this->container->get('router')->generate('home'));
            }else {
                return new RedirectResponse($referer);
            }
        }
        $request = $this->container->get('request');
        $calendarEvent->addParticipant($me);
        $em->persist($calendarEvent);

        $actionManager = $this->get('spy_timeline.action_manager');
        $subject       = $actionManager->findOrCreateComponent('\User', $me->getSlug());
        $em->persist($subject);
        $action        = $actionManager->create($subject,  'participe a un evenement',
            array(
                'directComplement' => ($calendarEvent->getAbsolutePath() == null ? '' : $calendarEvent->getAbsolutePath()),
                'slug' => $calendarEvent->getSlug() ));

        $actionManager->updateAction($action);

        $em->flush();

        $referer = $request->headers->get('referer');
        if (!$referer){
            return new RedirectResponse($this->container->get('router')->generate('home'));
        }else {
            return new RedirectResponse($referer);
        }


    }

    public function supprimerAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $calendarEvent = $em->getRepository('PortailBundle:CalendarEvent')
            ->findOneBy(array('slug' => $slug));


        if (!$calendarEvent)
        {
            throw new NotFoundHttpException("Horaire non trouvé");
        }

        $em->remove($calendarEvent);
        $em->flush();


        return new RedirectResponse($this->container->get('router')->generate('ltm_calendarevent_lister'));
    }
}
