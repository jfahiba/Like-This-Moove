<?php

namespace LTM\PortailBundle\Controller;

use LTM\PortailBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{


    public function attenteAction() {

        $em = $this->container->get('doctrine')->getManager();

        $context = $this->get('security.context');
        $user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }


        return $this->container->get('templating')->renderResponse(
            'PortailBundle:User:attente.html.twig',
            array(
                'user' => $user
            ));
    }

    public function listerprofesseurAction($styled = null) {
        return $this->listerAction(true, false, $styled);
    }
	
	public function listerschoolAction($styled = null){
		 return $this->listerAction(false, true, $styled);
	}
	
    public function listerAction($tuto = false, $school = false, $styled = null)
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
        $city = $request->get('city');

        $birthDateInferior = $request->get('birthDateInferior');
        $birthDateSuperior = $request->get('birthDateSuperior');
        $mooverDate = $request->get('mooverDate');


        /*
        $gender = $request->get('gender');
        if ($gender == 'Homme') {
            $user->setGender( '0');
        }elseif ($gender == 'Femme') {
            $user->setGender( '1');
        }*/

        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('PortailBundle:User', 'u');
		if ($tuto) {
			$qb->innerJoin(
            'PortailBundle:VideoTuto',
            'v', \Doctrine\ORM\Query\Expr\Join::WITH,
            'v.author = u.id');
		}
        
        if ($username != null) {
            $qb->andWhere(
                $qb->expr()->like('u.username', ':username') . ' OR '.
                $qb->expr()->like('u.lastName', ':username') . ' OR '.
                $qb->expr()->like('u.firstName', ':username') )
                ->setParameter('username', '%'.$username.'%');
        }
        if ($city != null) {
            $qb->andWhere(
                $qb->expr()->like('u.city', ':city') . ' OR '.
                $qb->expr()->like('u.country', ':city'))
                ->setParameter('city', '%'.$city.'%');
        }

		if ($school) {
			$qb->andWhere('u.gender  = 2');
			
			$qbc = $em->createQueryBuilder();
                $qbc->select('c')
                    ->from('PortailBundle:CCompte', 'c')
                    ->where($qbc->expr()->in('c.nom', '?1'))
                    ->setParameter('1', array('Pro', 'Pro (Essai gratuit 14 jours)' ));
                $categoryVal = $qbc->getQuery()->getResult();

                $qb->andWhere(
                    $qb->expr()->eq('u.category', $categoryVal["0"]->getId()  ) . ' OR '.
                    $qb->expr()->eq('u.category', $categoryVal["1"]->getId() ));
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

            $qb->andWhere(':tag MEMBER OF u.style')
                ->setParameter('tag', $styleVal);

        }

        $category = $request->get('category');

        if ($category != null && $category != "") {

            if ($category == 'Professionnel') {
                $qbc = $em->createQueryBuilder();
                $qbc->select('c')
                    ->from('PortailBundle:CCompte', 'c')
                    ->where($qbc->expr()->in('c.nom', '?1'))
                    ->setParameter('1', array('Pro', 'Pro (Essai gratuit 14 jours)' ));
                $categoryVal = $qbc->getQuery()->getResult();

                $qb->andWhere(
                    $qb->expr()->eq('u.category', $categoryVal["0"]->getId()  ) . ' OR '.
                    $qb->expr()->eq('u.category', $categoryVal["1"]->getId() ));

            } else {

                $categoryVal = $em
                    ->getRepository('PortailBundle:CCompte')
                    ->findOneBy(array('nom' => $category));
                $qb->andWhere(
                    $qb->expr()->eq('u.category', ':category'))
                    ->setParameter('category', $categoryVal->getId());
            }
            // "Pro (Essai gratuit 14 jours)"

        }

        if ($mooverDate != "") {

            $mooverD = $this->parseDate(date_parse_from_format('d/m/Y',$mooverDate));

            $qb->andWhere('u.mooverDate >= :date_from');
            $qb->setParameter('date_from', $mooverD, \Doctrine\DBAL\Types\Type::DATE);


        }

        if ($birthDateSuperior != "") {

            $birthDateSup = $this->parseDate(date_parse_from_format('d/m/Y', $birthDateSuperior));
            $qb->andWhere('u.birthDate >= :date_from');
            $qb->setParameter('date_from', $birthDateSup, \Doctrine\DBAL\Types\Type::DATE);


        }
        if ($birthDateInferior != "") {

            $birthDateInf = $this->parseDate(date_parse_from_format('d/m/Y',$birthDateInferior));

            $qb->andWhere( 'u.birthDate <= :date_to');
            $qb->setParameter('date_to', $birthDateInf, \Doctrine\DBAL\Types\Type::DATE);

        }
		 
        $qb->orderBy('u.mooverDate', 'DESC');
		
		$context = $this->get('security.context');
		if ($context->getToken()->getUser() == 'anon.') {
			 $qb->setMaxResults(14);
		}
		
		
        $users = $qb->getQuery()->getResult();

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $users,
            $pageNumber/*page number*/,
            14/*limit per page*/
        );


        $allstyles = $em
            ->getRepository('PortailBundle:CDance')->findAll();


        return $this->container->get('templating')->renderResponse('PortailBundle:User:lister.html.twig',
            array(
                'tuto' => $tuto,
				'school' => $school,
                'users' => $users,
                'pagination' => $pagination,
                'username' => (isset ($username)? $username : null),
                'category' => (isset ($category)? $category : null),
                'birthDateInferior' => (isset ($birthDateInferior)? $birthDateInferior : null),
                'birthDateSuperior' => (isset ($birthDateSuperior)? $birthDateSuperior : null),
                'mooverDate' => (isset ($mooverDate)? $mooverDate : null),
                'city' => (isset ($city)? $city : null),
                'styledanse'  => (isset ($styledanse)? $styledanse : null),
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

    public function topAction($max = 5)
    {
        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from('PortailBundle:User', 'a')
            ->orderBy('a.dateNaissance', 'ASC')
            ->setMaxResults($max);

        $query = $qb->getQuery();
        $users = $query->getResult();

        return $this->container->get('templating')->renderResponse('PortailBundle:User:liste.html.twig', array(
            'users' => $users
        ));
    }




    public function editerbychoiceAction($moovertype, $accounttype, $frequency, $duration){


        $message='';
        $em = $this->container->get('doctrine')->getManager();

        if (isset($slug))
        {
            $context = $this->get('security.context');
            $user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

            if (!$user)
            {
                $message='Aucun utilisateur trouvé';
                $user = new User();
                $category = $em
                    ->getRepository('PortailBundle:CPublish')
                    //->findOneBy(array('nom' => "Pro (Essai gratuit 14 jours)"));
                    ->findOneBy(array('nom' => "Semi Pro"));

                $user->setCategory($category);
                $user->getAbonnement()->setAboStatus('Trial');
                $startDate = new \DateTime("now");
                $user->getAbonnement()->getAboStartDate($startDate);
                $user->getAbonnement()->getAboEndDate($startDate->add(new \DateInterval('P14D')));

            }
        }
        else
        {
            // ajout d'un nouvel utilisateur
            $user = new User();
            $category = $em
                ->getRepository('PortailBundle:CPublish')
                //->findOneBy(array('nom' => "Pro (Essai gratuit 14 jours)"));
                ->findOneBy(array('nom' => "Semi Pro"));


            $user->setCategory($category);
            $user->getAbonnement()->setAboStatus('Trial');
            $startDate = new \DateTime("now");
            $user->getAbonnement()->getAboStartDate($startDate);
            $user->getAbonnement()->getAboEndDate($startDate->add(new \DateInterval('P14D')));
            $user->setMooverDate($startDate);

        }

        $flow = $this->get('ltm.form.flow.createUser'); // must match the flow's service id
        $options = array('moovertype' => $moovertype, 'accounttype' => $accounttype, 'frequency' => $frequency, 'duration' => $duration);
        $user->setOptions($options);
        $flow->bind($user);
        // form of the current step

        $form = $flow->createForm();


        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {

                // flow finished

                $request = $this->container->get('request');
                $user->file = $request->files->get("user")["file"];

                $user->upload();

                $em->persist($user);
                $em->flush();
                if (isset($slug))
                {
                    $message='Profil modifiée avec succès !';

                }
                else
                {
                    $message='Profil ajoutée avec succès !';

                }
                $flow->reset(); // remove step data from the session
                //return new RedirectResponse($this->container->get('router')->generate('ltm_usr_afficher', array('id' => $user->getId()) ));


                $route = $this->container->get('router')->generate('ltm_paypal_express_checkout_prepare_recurring_payment_agreement',
                    array('moovertype' => $moovertype, 'accounttype' => $accounttype, 'frequency' => $frequency, 'duration' => $duration));

                $request = $this->container->get('request');
                $session = $request->getSession();
                $session->set("abo_route", $route);

                return new RedirectResponse($route);

            }
        }

        return $this->render(
            'PortailBundle:User:register.html.twig',
            array('form' => $form->createView(),
                'flow' => $flow,
                'moovertype' => $moovertype, 'accounttype' => $accounttype, 'frequency' => $frequency , 'duration' => $duration
            )
        );
    }

    public function editerAction($slug = null)
    {
        $message='';
        $em = $this->container->get('doctrine')->getManager();

        if (isset($slug))
        {
            $context = $this->get('security.context');
            $user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

            if (!$user)
            {
                $message='Aucun utilisateur trouvé';
                $user = new User();
                $category = $em
                    ->getRepository('PortailBundle:CPublish')
                    //->findOneBy(array('nom' => "Pro (Essai gratuit 14 jours)"));
					->findOneBy(array('nom' => "Semi Pro"));

                $user->setCategory($category);
                $user->getAbonnement()->setAboStatus('Trial');
                $startDate = new \DateTime("now");
                $user->getAbonnement()->getAboStartDate($startDate);
                $user->getAbonnement()->getAboEndDate($startDate->add(new \DateInterval('P14D')));

            }
        }
        else
        {
            // ajout d'un nouvel utilisateur
            $user = new User();
            $category = $em
                ->getRepository('PortailBundle:CPublish')
                //->findOneBy(array('nom' => "Pro (Essai gratuit 14 jours)"));
				->findOneBy(array('nom' => "Semi Pro"));
				
				
            $user->setCategory($category);
            $user->getAbonnement()->setAboStatus('Trial');
            $startDate = new \DateTime("now");
            $user->getAbonnement()->getAboStartDate($startDate);
            $user->getAbonnement()->getAboEndDate($startDate->add(new \DateInterval('P14D')));
            $user->setMooverDate($startDate);

        }

        $flow = $this->get('ltm.form.flow.createUser'); // must match the flow's service id

        $flow->bind($user);
        // form of the current step
        $form = $flow->createForm();


        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {

                // flow finished

                $request = $this->container->get('request');
                $user->file = $request->files->get("user")["file"];

                $user->upload();

                $em->persist($user);
                $em->flush();
                if (isset($slug))
                {
                    $message='Profil modifiée avec succès !';

                }
                else
                {
                    $message='Profil ajoutée avec succès !';

                }
                $flow->reset(); // remove step data from the session
                //return new RedirectResponse($this->container->get('router')->generate('ltm_usr_afficher', array('id' => $user->getId()) ));


                return new RedirectResponse($this->container->get('router')->generate('ltm_payment_credit_pricing') );

            }
        }

        return $this->render(
            'PortailBundle:User:register.html.twig',
            array('form' => $form->createView(),
                'flow' => $flow)
        );
    }



    public function afficherAction()
    {
        $em = $this->container->get('doctrine')->getManager();
		$context = $this->get('security.context');
		$user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        $videos= $em->getRepository('PortailBundle:VideoTuto')
            ->findBy(array('author' => $user->getId()));

        $fans = $em->getRepository('PortailBundle:Relation')->findBy(
            array('star' => $user, 'category' => $this->findRelationCategory('Fan'))
        );
        $amis = $em->getRepository('PortailBundle:Relation')->findBy(
            array('star' => $user, 'category' => $this->findRelationCategory('Amitie'), 'isActive' => true)
        );
        $amisAsked = $em->getRepository('PortailBundle:Relation')->findBy(
            array('follower' => $user, 'category' => $this->findRelationCategory('Amitie'), 'isActive' => true)
        );

        $supports = $em->getRepository('PortailBundle:Relation')->findBy(
            array('star' => $user, 'category' => $this->findRelationCategory('Recommandation'), 'isActive' => true)
        );

        
        $ids = array();


        foreach ($amis as $relation) {
            $ids[] = $relation->getFollower()->getId();
        }
        foreach ($amisAsked as $relation) {
            $ids[] = $relation->getStar()->getId();
        }


        $qb = $this->container->get('doctrine')->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('PortailBundle:User', 'u')
            ->where($qb->expr()->in('u.id', '?1'))
            ->add('orderBy', 'u.lastLoginDate DESC')
            ->setParameter('1', $ids);
        $activeFriends = $qb->getQuery()->getResult();


		
        return $this->container->get('templating')->renderResponse('PortailBundle:User:afficher.html.twig', array(
            'user' => $user,
            'fans' => $fans == null? 0 : count($fans),
            'supports' => $supports == null? 0 : count($supports),
            'tutos' => $videos==null? 0 : count($videos),
            'amis' => ($amis == null? 0 : count($amis)) + (($amisAsked == null? 0 : count($amisAsked))) ,
            'activeFriends' => $activeFriends
        ));


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


    public function afficherpublicprofesseurAction($slug) {
        return $this->afficherpublicAction($slug, true);
    }

    public function afficherpublicAction($slug, $tuto = false)
    {

        $em = $this->container->get('doctrine')->getManager();

        $user = $em->getRepository('PortailBundle:User')
            ->findOneBy(array('slug' => $slug));


        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        $user->incrementVues($em);

        return $this->container->get('templating')->renderResponse('PortailBundle:User:afficherpublic.html.twig', array(
            'user' => $user,
            'tuto' => $tuto
        ));
    }




    public function historiquecreditAction() {
        $em = $this->container->get('doctrine')->getManager();

        try {
            $context = $this->get('security.context');
			$user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

            if (!$user)
            {
                throw new NotFoundHttpException("Utilisateur non trouvé");
            }


            return $this->container->get('templating')->renderResponse('PortailBundle:User:listeHistoriqueCredit.html.twig', array(
                'credit' => $user->getCredit(), 'tuto' => true
            ));
        }catch (Exception $e) {
            throw new NotFoundHttpException("Historique non trouvé");
        }


    }

    public function supprimerAction($slug)
    {
        $em = $this->container->get('doctrine')->getManager();
        $user = $em->getRepository('PortailBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        $em->remove($user);
        $em->flush();


        return new RedirectResponse($this->container->get('router')->generate('ltm_usr_lister'));
    }

    public function listerdanseursltmAction($title = null)
    {
        $em = $this->container->get('doctrine')->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('PortailBundle:CCompte', 'c')
            ->where($qb->expr()->in('c.nom', '?1'))
            ->setParameter('1', array('Pro', 'Pro (Essai gratuit 14 jours)' ));
        $category = $qb->getQuery()->getResult();


        $users = $em->getRepository('PortailBundle:User')->findBy(
            array(
                'category' => $category,
                'isActive' => true),
            array('mooverDate' => 'DESC')

        );


        return $this->container->get('templating')->renderResponse('PortailBundle:User:listePro.html.twig',
            array(
                'users' => $users,
                'relationTitle' => $title == null? $this->get('translator')->trans("pro.dancers") : $title,
                'relationClass' => '',
                'tuto' => false
            ));

    }


    public function onglet3Action()
    {

        $em = $this->container->get('doctrine')->getManager();

        $context = $this->get('security.context');
		$user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }
        $post = Request::createFromGlobals();

        if ($post->request->has('submit')) {

            $user->setUsername($post->request->get('username'));
            $user->setFirstName( $post->request->get('firstName'));
            $user->setLastName( $post->request->get('lastName'));
			
			$qbc = $em->createQueryBuilder();
			$qbc->select('c')
				->from('PortailBundle:CDance', 'c')
				->where($qbc->expr()->in('c.nom', '?1'))
				->setParameter('1', $post->request->get('styledanse'));
			$categoryVal = $qbc->getQuery()->getResult();
			$user->setStyle($categoryVal);
			
            $bDay = date_parse($post->request->get('birthDate'));
            $dateTime = new \DateTime('now');

            $dateTime->setDate($bDay["year"],
                $bDay["month"],
                $bDay["day"]);
            $user->setBirthDate($dateTime);
            $gender = $post->request->get('gender');
            if ($gender == 'Danseur') {
                $user->setGender( '0');
            }elseif ($gender == 'Danseuse') {
                $user->setGender( '1');
            }elseif ($gender == 'Ecole') {
                $user->setGender( '2');
            }elseif ($gender == 'Association') {
                $user->setGender( '3');
            } elseif ($gender == 'Recruteur') {
                $user->setGender( '4');
            }elseif ($gender == 'Recruteuse') {
                $user->setGender( '5');
            } elseif($gender == 'Agence de Recrutement') {
				$user->setGender( '6'); 
			} elseif($gender == 'Visiteur') {
				$user->setGender( '7'); 
			}

            $user->setAPropos( $post->request->get('aboutMe'));
			$user->setActualite( $post->request->get('actualite'));
            $user->setBiographie( $post->request->get('biographie'));
            $user->setAwards( $post->request->get('awards'));
            $user->setContrats( $post->request->get('contrats'));

            $user->setEmail($post->request->get('email'));
            $user->setCountry($post->request->get('country'));
            $user->setCity($post->request->get('city'));
            $user->setWebSite( $post->request->get('webSite'));
            $user->setPhone( $post->request->get('phone'));
            $user->setAgentPhone( $post->request->get('agentPhone'));

            $user->setFacebook( $post->request->get('facebook'));
            $user->setTwitter( $post->request->get('twitter'));
            $user->setGoogleplus( $post->request->get('googleplus'));
            $user->setSkype( $post->request->get('skype'));
            $user->setPublishProfil($post->request->get('publishProfil')!= "");
            $user->setReceiveMailDigests( $post->request->get('receiveMailDigests') != "");
            $user->setKeepHistoric( $post->request->get('keepHistoric') != null);
            $user->setHistoricDays( $post->request->get('historicDays'));
            $user->setHeight($post->request->get('height'));
            $user->setShowPersonalInformation($post->request->get('showPersonalInformation')!= "");

            $user->file = $post->files->get('file');
            $user->fileCover = $post->files->get('fileCover');
            $password= $post->request->get('password');
            $confirm= $post->request->get('confirm');
            if ($password!=null && $password == $confirm) {
                $user->setPlainPassword($password);
            }
            $user->upload();
            $user->uploadCover();

            $em->persist($user);
            $em->flush();

        } else {

        }
		
		$allstyles = $em
            ->getRepository('PortailBundle:CDance')->findAll();
		
		
			
        return $this->render('PortailBundle:User:onglet3.html.twig',
            array( 'user' => $user,
				'allstyles' => $allstyles,
                'reload' => $post->request->has('submit'),
                'avancement' => $user->getProfilEditAvancement()));


    }
	
	
}
