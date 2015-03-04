<?php


namespace LTM\PortailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LTM\PortailBundle\Entity\Categorie;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->container->get('doctrine')->getManager();
        $categories = $em->getRepository('PortailBundle:Categorie')->findAll();

        return $this->container->get('templating')->renderResponse('PortailBundle:Default:index.html.twig',array(
                'categories' => $categories)
        );
    }

    public function indextutoAction()
    {
        //$em = $this->container->get('doctrine')->getManager();
		 
        return $this->container->get('templating')->renderResponse('PortailBundle:Default:indextuto.html.twig');
				
    }

    public function aproposAction()
    {
        //$em = $this->container->get('doctrine')->getManager();
		 
        return $this->container->get('templating')->renderResponse('PortailBundle:Default:apropos.html.twig');
				
    }
    public function conditionsAction()
    {
        //$em = $this->container->get('doctrine')->getManager();
		 
        return $this->container->get('templating')->renderResponse('PortailBundle:Default:conditions.html.twig');
				
    }
    public function recrutementAction()
    {
        //$em = $this->container->get('doctrine')->getManager();
		 
        return $this->container->get('templating')->renderResponse('PortailBundle:Default:recrutement.html.twig');
				
    }
    public function contactAction()
    {

        $post = Request::createFromGlobals();

        if ($post->request->has('submit')) {
            $email = $post->request->get('email');
            $name = $post->request->get('name');
            $message = $post->request->get('message');

            try {
                if ($email != null) {
                    $swiftMessage = \Swift_Message::newInstance()
                        ->setSubject('[LTM][Contact] ' . $email )
                        ->setFrom($email)
                        ->setTo('do.not.answer.ltm@gmail.com')
                        ->setBody(
                            $this->renderView(
                                'PortailBundle:Message:email_contact.html.twig',
                                array('sender' => $name, 'message' => $message)

                            ), 'text/html'
                        )
                    ;
                    $this->get('mailer')->send($swiftMessage);
                    ?>
                    <script language='Javascript'>
                        alert('Votre message a ete bien transmis');
                    </script>
                <?php
                }
            }catch(\Exception $e){
                ?>
                <script language='Javascript'>
                    alert('Nous n\'avons pu transmettre votre message');
                </script>
            <?php

            }
        }
        return $this->container->get('templating')->renderResponse('PortailBundle:Default:contact.html.twig');

    }




    public function forfaitTypeAction($moovertype){
        if ($moovertype != 'dancer' and $moovertype!= 'school' and $moovertype != 'recruiter') {
            $moovertype = 'dancer';

            ?>
            <script language='Javascript'>
                alert('Type de forfait inconnu');
            </script>
            <?php
         }

		$url = $moovertype == 'dancer' ? 'PortailBundle:Default:forfaittypedancer.html.twig':
                ($moovertype == 'recruiter' ? 'PortailBundle:Default:forfaittyperecruiter.html.twig':
					($moovertype == 'school' ? 'PortailBundle:Default:forfaittypeschool.html.twig':
						'PortailBundle:Default:forfaittype.html.twig'));
	
        return $this->container->get('templating')->renderResponse($url , array( 'moovertype' => $moovertype));


    }

	public function forfaitDurationAction($moovertype, $accounttype){
        if ($accounttype != 'min' and $accounttype!= 'avg' and $accounttype != 'max') {
            $moovertype = 'min';

            ?>
            <script language='Javascript'>
                alert('Type de forfait inconnu');
            </script>
        <?php
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:Default:forfaitduration.html.twig', array(
            'moovertype' => $moovertype, 'accounttype' => $accounttype
        ));
    } 
	
    public function forfaitFrequencyAction($moovertype, $accounttype, $duration){
        
        return $this->container->get('templating')->renderResponse('PortailBundle:Default:forfaitfrequency.html.twig', array(
            'moovertype' => $moovertype, 'accounttype' => $accounttype, 'duration' => $duration
        ));
    }
	
	
	
	public function newsletterAction()
    {
        //$em = $this->container->get('doctrine')->getManager();
		$post = Request::createFromGlobals();
		 
        if ($post->request->has('submit')) {
			$email = $post->request->get('email');
			try {
				if ($email != null) {
					$swiftMessage = \Swift_Message::newInstance()
						->setSubject('[LTM][Newsletter] ' . $email )
						->setFrom('do_not_answer@likethismoove.com')
						->setTo('do.not.answer.ltm@gmail.com')
						->setBody(
							$this->renderView(
								'PortailBundle:Message:email_newsletter.html.twig',
								array('sender' => $email)
							
							), 'text/html'
						)
						;
					$this->get('mailer')->send($swiftMessage);
                    ?>
                    <script language='Javascript'>
                        alert('Vous etes bien inscrit(e) a la newsletter');
                    </script>
                <?php
				}
			}catch(\Exception $e){
                ?>
                <script language='Javascript'>
                    alert('Nous n\'avons pu vous inscrire a la newsletter');
                </script>
            <?php

            }
		}
        return $this->container->get('templating')->renderResponse('PortailBundle:Default:newsletter.html.twig');
				
    }

    public function createslugsAction()
    {
        $em = $this->container->get('doctrine')->getManager();

        $events = $em
            ->getRepository('PortailBundle:CalendarEvent')->findAll();

        $users = $em
            ->getRepository('PortailBundle:User')->findAll();

        $videos = $em
            ->getRepository('PortailBundle:Video')->findAll();

        $tutos = $em
            ->getRepository('PortailBundle:VideoTuto')->findAll();

        $images = $em
            ->getRepository('PortailBundle:Image')->findAll();


        $pubs = $em
            ->getRepository('PortailBundle:Advertisement')->findAll();

        $threads = $em
            ->getRepository('PortailBundle:Thread')->findAll();


        foreach ($events as $val) {
            $val->generateSlug();
            $em->persist($val);
        }
        foreach ($users as $val) {
            $val->generateSlug();
            $em->persist($val);
        }
        foreach ($videos as $val) {
            $val->generateSlug();
            $em->persist($val);
        }
        foreach ($tutos as $val) {
            $val->generateSlug();
            $em->persist($val);
        }
        foreach ($images as $val) {
            $val->generateSlug();
            $em->persist($val);
        }
        foreach ($pubs as $val) {
            $val->generateSlug();
            $em->persist($val);
        }

        foreach ($threads as $val) {
            $val->generateSlug();
            $em->persist($val);
        }

        $em->flush();

        return $this->container->get('templating')->renderResponse('PortailBundle:Default:index.html.twig');

    }



}
