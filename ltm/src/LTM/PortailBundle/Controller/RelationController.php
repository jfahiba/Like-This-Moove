<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\Relation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class RelationController extends Controller
{

    public function editerfanAction($slug = null)
    {
        return $this->editerAction($slug, 'Fan', false, false);
    }
    public function editerfanrefuseAction($slug = null)
    {
        return $this->editerAction($slug, 'Fan', false, true);
    }
    public function editerfriendsendAction($slug = null)
    {
        return $this->editerAction($slug, 'Amitie', false,false);
    }
    public function editerfriendacceptAction($slug = null)
    {
        return $this->editerAction($slug, 'Amitie', true, false);
    }
    public function editerfriendrefuseAction($slug = null)
    {
        return $this->editerAction($slug, 'Amitie', true, true);
    }
    public function editerrecommandsendAction($slug = null)
    {
        return $this->editerAction($slug, 'Recommandation', false,false);
    }
    public function editerrecommandacceptAction($slug = null)
    {
        return $this->editerAction($slug, 'Recommandation', true,false);
    }
    public function editerrecommandrefuseAction($slug = null)
    {
        return $this->editerAction($slug, 'Recommandation', true, true);
    }

    public function editerAction($slug , $category, $modifyExisting= false, $delete)
    {

        $em = $this->container->get('doctrine')->getManager();
        if ( !isset($slug))
        {
            throw new NotFoundHttpException("Pair non trouvé");
        }
        $pair = $em->getRepository('PortailBundle:User')
            ->findOneBy(array('slug' => $slug));


        if (!$pair)
        {
            throw new NotFoundHttpException("Pair non trouvé");
        }
        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId()); ;

        // Relation categorie
        if (!$category)
        {
            throw new NotFoundHttpException("Relation non trouvé");
        }
        $relationCategory = $this->findRelationCategory($category);
	
        if ($modifyExisting) {

            $relation= $em->getRepository('PortailBundle:Relation')->findOneBy(
                array('star' => $me,
                    'category' => $relationCategory,
                    'follower' => $pair)

            );


            if (!$delete) {
                $relation->setIsActive(true);
            }

        } else {

            // Si exite deja, ne rien faire
            $existingrelation= $em->getRepository('PortailBundle:Relation')->findOneBy(
                array('star' => $pair,
                    'category' => $relationCategory,
                    'follower' => $me)

            );
            if ($existingrelation != null) {
                 $request = $this->container->get('request');
                $referer = $request->headers->get('referer');
                if (!$referer){
                    return new RedirectResponse($this->container->get('router')->generate('home'));
                }else {
                    return new RedirectResponse($referer);
                }
            }
            // ajout d'une nouvelle relation
            $relation = new Relation();

            $relation->setCategory($relationCategory);
            $relation->setCreationDate(new \DateTime("now"));
            $relation->setStar($pair);
            $relation->setFollower($me);

            if ($category == 'Amitie' || $category =='Recommandation') {
                $relation->setIsActive(false);
				
				try {
					if ($pair->getEmail() != null) {
						$swiftMessage = \Swift_Message::newInstance()
							->setSubject('[LTM]['. $category .'] Nouvelle demande')
							->setFrom('do_not_answer@likethismoove.com')
							->setTo($pair->getEmail())
							->setBody(
								$this->renderView(
									'PortailBundle:Message:email_relation.html.twig',
									array('subject' => $category,
										  'participant' => $pair,
										  'sender' => $me)
								
								), 'text/html'
							)
							;
						$this->get('mailer')->send($swiftMessage);
					}
				}catch(\Exception $e){}
			
            } else {
                $relation->setIsActive(true);
            }

        }
        $request = $this->container->get('request');
        if ($delete) {
            $em->remove($relation);
        }else{
            $em->persist($relation);

            $actionManager = $this->get('spy_timeline.action_manager');
            $subject       = $actionManager->findOrCreateComponent('\User', $me->getSlug());
            $em->persist($subject);
            $action        = $actionManager->create($subject,  $category,
                array('slug' => $pair->getSlug(),
                    'inverse' => $modifyExisting) );
            $actionManager->updateAction($action);
        }
        $em->flush();

        $referer = $request->headers->get('referer');
        if (!$referer){
            return new RedirectResponse($this->container->get('router')->generate('home'));
        }else {
            return new RedirectResponse($referer);
        }


    }

    private function findRelationCategory ($name){

        $relation = $this->container->get('doctrine')
            ->getRepository('PortailBundle:CPublish')
            ->findOneBy(array('nom' => $name));

        if (!$relation) {
            throw $this->createNotFoundException(
                'No relation found for name '.$name
            );
        }
        return $relation;

    }


    public function listerfollowerAction($slug = null, $tuto = false)
    {

        return $this->listerAction($this->get('translator')->trans("followers"), $slug, 'Fan', false, false,"",  $tuto);
    }
    public function listerfollowingAction($slug = null, $tuto = false)
    {
        return $this->listerAction($this->get('translator')->trans("following"),$slug, 'Fan', true, false, "", $tuto);
    }
    public function listerfriendAction($slug = null, $tuto = false)
    {
        return $this->listerAction($this->get('translator')->trans("friends"), $slug, 'Amitie', false, false, "", $tuto);
    }
    public function listerfriendacceptAction( $tuto = false)
    {
        return $this->listerAction($this->get('translator')->trans("friends.to.accept"), null, 'Amitie', false, true, "",  $tuto);
    }

    public function listerrecommandationAction($slug = null, $tuto = false)
    {

        return $this->listerAction($this->get('translator')->trans("my.references"), $slug, 'Recommandation', false, false, "", $tuto);
    }


    public function listerrecommandationsendAction($slug = null, $tuto = false)
    {
        return $this->listerAction($this->get('translator')->trans("i.recommand"), $slug, 'Recommandation', true, false, "-my", $tuto);
    }

    public function listerrecommandationacceptAction($tuto = false)
    {
        return $this->listerAction($this->get('translator')->trans("reco.to.accept"), null, 'Recommandation', false, true, "", $tuto);
    }

    public function listerAction($relationTitle, $slug, $category, $inverse = false, $pending = false, $relationClass = "", $tuto = false)
    {


        $em = $this->container->get('doctrine')->getManager();

        // Relation
        $relationCategory = $this->findRelationCategory($category);

        if ($pending == true) {

            $me = $this->get('security.context')->getToken()->getUser();

            $relations= $em->getRepository('PortailBundle:Relation')->findBy(
                array('star' => $me,
                    'category' => $relationCategory,
                    'isActive' => false),
                array('creationDate' => 'DESC')

                );
        } else {

            if (!$slug)
            {
                throw new NotFoundHttpException("Relation non realisée");
            }


            // Star
            $star = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));


            if (!$star)
            {
                throw new NotFoundHttpException("Utilisateur non trouvé");
            }


            if ($inverse == true) {
                // Following
                // Recommending
                // Asking for recommandation
                // Friends ask

                $relations= $em->getRepository('PortailBundle:Relation')->findBy(
                    array('follower' => $star, 'category' => $relationCategory, 'isActive' => true),
                    array('creationDate' => 'DESC')
                );
            } else {
                // followers
                // Recommendedby
                // friends accept

                $relations= $em->getRepository('PortailBundle:Relation')->findBy(
                    array('star' => $star, 'category' => $relationCategory, 'isActive' => true),
                    array('creationDate' => ' DESC')
                );

            }
        }

        $ids = array();
        foreach ($relations as $relation) {
            $ids[] = $inverse? $relation->getStar()->getId() :$relation->getFollower()->getId();
        }

        $qb = $this->container->get('doctrine')->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('PortailBundle:User', 'u')
            ->where($qb->expr()->in('u.id', '?1'))
            ->setParameter('1', $ids);
        $users = $qb->getQuery()->getResult();



        if ($pending){
            return $this->container->get('templating')->renderResponse('PortailBundle:User:listePending.html.twig',
                array(
                    'users' => $users,
                    'relationTitle' => $relationTitle,
                    'category' => $category,
                    'tuto' => $tuto
                ));
        } elseif ('Recommandation' == $category) {
            return $this->container->get('templating')->renderResponse('PortailBundle:User:listePro.html.twig',
                array(
                    'users' => $users,
                    'relationTitle' => $relationTitle,
                    'relationClass' => $relationClass,
                    'tuto' => $tuto
                ));
        } else {

        return $this->container->get('templating')->renderResponse('PortailBundle:User:listeGauche.html.twig',
            array(
                'users' => $users,
                'relationTitle' => $relationTitle . '(' . count($users) . ')',
                'tuto' => $tuto
            ));
        }

    }



    public function topAction($max = 5, $slug = null)
    {
        $em = $this->container->get('doctrine')->getManager();


        if ($slug != null) {
            $star = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));

            $relations = $em->getRepository('PortailBundle:Relation')->findBy(
                array('star' => $star),
                array('creationDate' => 'DESC'),
                $max,
                null
            );

        } else {
            $relations = $em->getRepository('PortailBundle:Relation')->findBy(
                array('creationDate' => 'DESC'),
                $max,
                null
            );
        }
        return $this->container->get('templating')->renderResponse('PortailBundle:Relation:liste.html.twig', array(
            'relations' => $relations,
        ));
    }




    public function afficherAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $relation = $em->getRepository('PortailBundle:Relation')
            ->findOneBy(array('slug' => $slug));


        if (!$relation)
        {
            throw new NotFoundHttpException("Relation non trouvée");
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:Relation:afficher.html.twig', array(
            'relation' => $relation,
        ));


    }

    public function supprimerAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $relation = $em->getRepository('PortailBundle:Relation')
            ->findOneBy(array('slug' => $slug));

        if (!$relation)
        {
            throw new NotFoundHttpException("Relation non trouvée");
        }

        $em->remove($relation);
        $em->flush();


        return new RedirectResponse($this->container->get('router')->generate('ltm_relation_lister'));
    }
}
