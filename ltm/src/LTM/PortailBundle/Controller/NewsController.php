<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\News;
use LTM\PortailBundle\Form\NewsForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{

    public function editerAction($id = null)
    {
        $message='';
        $em = $this->container->get('doctrine')->getManager();

        if (isset($id))
        {
            // modification d'une nouvelle existant : on recherche ses données
            $news = $em->find('PortailBundle:News', $id);

            if (!$news)
            {
                $message='Aucune nouvelle trouvée';
                $news = new News();
            }
        }
        else
        {
            // ajout d'une nouvelle
            $news = new News();
        }

        $form = $this->container->get('form.factory')->create(new NewsForm(), $news);

        $request = $this->container->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em->persist($news);
                $em->flush();
                if (isset($id))
                {
                    $message='Nouvelle modifiée avec succès !';
                }
                else
                {
                    $message='Nouvelle ajoutée avec succès !';
                }
            }
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:News:editer.html.twig',
            array(
                'form' => $form->createView(),
                'message' => $message,
            ));
    }


    public function listerAction()
    {
        $em = $this->container->get('doctrine')->getManager();

        $news= $em->getRepository('PortailBundle:News')->findAll();

        return $this->container->get('templating')->renderResponse('PortailBundle:News:lister.html.twig',
            array(
                'news' => $news
            ));

    }

    public function listerTousAction()
    {
        $em = $this->container->get('doctrine')->getManager();

        $news= $em->getRepository('PortailBundle:News')->findAll();

        return $this->container->get('templating')->renderResponse('PortailBundle:News:lister.html.twig',
            array(
                'news' => $news
            ));

    }


    public function topAction($max = 5)
    {
        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('n')
            ->from('PortailBundle:News', 'n')
            ->orderBy('n.creationDate', 'DESC')
            ->setMaxResults($max);

        $query = $qb->getQuery();
        $news = $query->getResult();

        return $this->container->get('templating')->renderResponse('PortailBundle:News:liste.html.twig', array(
            'news' => $news,
        ));
    }

    public function topTousAction($max = 5)
    {
        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('n')
            ->from('PortailBundle:News', 'n')
            ->orderBy('n.creationDate', 'DESC')
            ->setMaxResults($max);

        $query = $qb->getQuery();
        $news = $query->getResult();

        return $this->container->get('templating')->renderResponse('PortailBundle:News:liste.html.twig', array(
            'news' => $news,
        ));
    }


    public function afficherAction($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $news = $em->find('PortailBundle:News', $id);

        if (!$news)
        {
            throw new NotFoundHttpException("Nouvelle non trouvée");
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:News:afficher.html.twig', array(
            'news' => $news,
        ));


    }

    public function supprimerAction($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $news = $em->find('PortailBundle:User', $id);

        if (!$news)
        {
            throw new NotFoundHttpException("Nouvelle non trouvée");
        }

        $em->remove($news);
        $em->flush();


        return new RedirectResponse($this->container->get('router')->generate('ltm_news_lister'));
    }
}
