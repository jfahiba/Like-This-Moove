<?php

namespace LTM\PortailBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );

        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);

        } else {
            $error = '';

            $request = $this->container->get('request');
            $session->set("login_referer", $request->headers->get('referer'));


        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render(
            'PortailBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error
            )
        );
    }

    public function dumpStringAction()
    {
        return $this->render('PortailBundle:Security:dumpString.html.twig', array());

    }

    public function fbloginAction()
    {
        return $this->render('PortailBundle:Security:fblogin.html.twig', array());
    }
	
	public function forgotpasswordAction()
    {
	$post = Request::createFromGlobals();
	$message = '';
        if ($post->request->has('submit')) {
            $request = $post->request;
        
			$username = $request->get('username');
			$email = $request->get('email');
		   
			$em = $this->container->get('doctrine')->getManager();

			$qb = $em->createQueryBuilder();
			$qb->select('u')
				->from('PortailBundle:User', 'u');
			$qb->andWhere(
					$qb->expr()->like('u.username', ':username') . ' OR '.
					$qb->expr()->like('u.lastName', ':username') . ' OR '.
					$qb->expr()->like('u.firstName', ':username') )
					->setParameter('username', '%'.$username.'%');
			 $qb->andWhere(
						$qb->expr()->eq('u.email', ':email'))
						->setParameter('email', $email);
			$qb->setMaxResults(1);
			$users = $qb->getQuery()->getResult();		
			
			try {
					if (count($users) == 1) {
						$user = $users[0];
						$passTemp = uniqid(null, false);
						$user->setPlainPassword($passTemp);
						$em->persist($user); 
						$em->flush();
						$swiftMessage = \Swift_Message::newInstance()
							->setSubject('[LTM] Modification de votre code d\'acces')
							->setFrom('do_not_answer@likethismoove.com')
							->setTo($email)
							->setBody(
								$this->renderView(
									'PortailBundle:Message:email_forgotpasspord.html.twig',
									array('code' => $passTemp,
										  'participant' => $user)							
								), 'text/html'
							)
							;
						$this->get('mailer')->send($swiftMessage);
						$message = 'Votre code d\'acces vous a ete envoye par email';
					} else {
						$message = 'Vos identifiants ne correspondent a aucun moover.';
					}
				}catch(\Exception $e){
					$message = 'Vos identifiants ne correspondent a aucun moover.';
				}
			}
        return $this->render(
            'PortailBundle:Security:forgotpassword.html.twig',
            array( 'message' => $message)
        );
    }

}