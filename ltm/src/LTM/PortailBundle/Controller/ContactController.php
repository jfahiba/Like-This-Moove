<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\User;
use LTM\PortailBundle\Form\UserForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use LTM\PortailBundle\Form\Model\Registration;
use LTM\PortailBundle\Form\RegistrationForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ContactController extends Controller
{



    public function editerAction($id = null)
    {
        $message='';
        $em = $this->container->get('doctrine')->getManager();

        if (isset($id))
        {
            // modification d'un evenement existant : on recherche ses données
            $calendarEvent = $em->find('PortailBundle:CalendarEvent', $id);

            if (!$calendarEvent)
            {
                $message='Aucun evenement de calendrier trouvé';
                $calendarEvent = new CalendarEvent();
            }
        }
        else
        {
            // ajout d'un nouvel acteur
            $calendarEvent = new CalendarEvent();
        }

        $form = $this->container->get('form.factory')->create(new CalendarEventForm(), $calendarEvent);

        $request = $this->container->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em->persist($calendarEvent);
                $em->flush();
                if (isset($id))
                {
                    $message='Horaire modifié avec succès !';
                }
                else
                {
                    $message='Horaire ajouté avec succès !';
                }
            }
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:CalendarEvent:editer.html.twig',
            array(
                'form' => $form->createView(),
                'message' => $message,
            ));
    }


    public function listerAction()
    {
        $em = $this->container->get('doctrine')->getManager();

        $calendarEvents= $em->getRepository('PortailBundle:CalendarEvent')->findAll();

        return $this->container->get('templating')->renderResponse('PortailBundle:CalendarEvent:lister.html.twig',
            array(
                'calendarEvents' => $calendarEvents
            ));

    }

    public function topAction($max = 5)
    {
        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('PortailBundle:CalendarEvent', 'c')
            ->orderBy('c.start', 'ASC')
            ->setMaxResults($max);

        $query = $qb->getQuery();
        $calendarEvents = $query->getResult();

        return $this->container->get('templating')->renderResponse('PortailBundle:CalendarEvent:liste.html.twig', array(
            'calendarEvents' => $calendarEvents,
        ));
    }


    public function afficherAction($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $calendarEvent = $em->find('PortailBundle:CalendarEvent', $id);

        if (!$calendarEvent)
        {
            throw new NotFoundHttpException("Horaire non trouvé");
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:CalendarEvent:afficher.html.twig', array(
            'calendarEvent' => $calendarEvent,
        ));


    }

    public function supprimerAction($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $calendarEvent = $em->find('PortailBundle:User', $id);

        if (!$calendarEvent)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        $em->remove($calendarEvent);
        $em->flush();


        return new RedirectResponse($this->container->get('router')->generate('ltm_lister'));
    }
}
