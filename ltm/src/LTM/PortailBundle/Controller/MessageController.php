<?php

namespace LTM\PortailBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\MessageBundle\Provider\ProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class MessageController extends Controller
{

    /**
     * Displays the authenticated participant inbox
     *
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->getProvider()->getInboxThreads();

        $em = $this->container->get('doctrine')->getManager();
        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId());

        return $this->container->get('templating')->renderResponse('PortailBundle:Message:inbox.html.twig', array(
            'threads' => $threads,
            'user' => $me,
            'tabselected' => 'inbox'
        ));
    }

    /**
     * Displays the authenticated participant messages sent
     *
     * @return Response
     */
    public function sentAction()
    {

        $threads = $this->getProvider()->getSentThreads();

        $em = $this->container->get('doctrine')->getManager();
        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId());

        return $this->container->get('templating')->renderResponse('PortailBundle:Message:sent.html.twig', array(
            'threads' => $threads,
            'user' => $me,
            'tabselected' => 'sent'
        ));
    }

    /**
     * Displays the authenticated participant deleted threads
     *
     * @return Response
     */
    public function deletedAction()
    {
        $threads = $this->getProvider()->getDeletedThreads();

        $em = $this->container->get('doctrine')->getManager();
        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId());

        
        return $this->container->get('templating')->renderResponse('PortailBundle:Message:deleted.html.twig', array(
            'threads' => $threads,
            'user' => $me,
            'tabselected' => 'delete'
        ));
    }

    /**
     * Displays a thread, also allows to reply to it
     *
     * @param string $slug the thread id
     *
     * @return Response
     */
    public function threadAction($threadId, $slug)
    {
        $em = $this->container->get('doctrine')->getManager();

        $thread = $this->getProvider()->getThread($threadId);
        $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('fos_message.reply_form.handler');


        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId());

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('ltm_message_thread_view', array(
                'slug' => $message->getThread()->getSlug(),
                'threadId' => $message->getThread()->getId(),
                'user' => $me,
                'tabselected' => ''
            )));
        }

        return $this->container->get('templating')->renderResponse('PortailBundle:Message:thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread,
            'user' => $me,
            'tabselected' => ''
        ));
    }

    /**
     * Create a new message thread
     *
     * @return Response
     */
    public function newThreadAction()
    {
        $form = $this->container->get('fos_message.new_thread_form.factory')->create();
        $formHandler = $this->container->get('fos_message.new_thread_form.handler');

        $em = $this->container->get('doctrine')->getManager();
        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId());

       
        if ($message = $formHandler->process($form)) {
			
			$meta = $message->getMetadata()[0];
			try {
				if ($meta->getParticipant()->getEmail() != null) {
					$swiftMessage = \Swift_Message::newInstance()
						->setSubject('[LTM] Nouveau message de ' . $me->getUsername())
						->setFrom('do_not_answer@likethismoove.com')
						->setTo($meta->getParticipant()->getEmail())
						->setBody(
							$this->renderView(
								'PortailBundle:Message:email.html.twig',
								array('subject' => $message->getThread()->getSubject(), 
									  'body' => $message->getBody(),
									  'participant' => $meta->getParticipant(),
									  'sender' => $me)
							
							), 'text/html'
						)
						;
					$this->get('mailer')->send($swiftMessage);
				}
			}catch(\Exception $e){}
	
            return new RedirectResponse($this->container->get('router')->generate('ltm_message_thread_view', array(
                'slug' => $message->getThread()->getSlug(),
                'threadId' => $message->getThread()->getId(),
            )));
        }

        $qb = $em->createQueryBuilder();
        
		$amitie = $this->findRelationCategory('Amitie'); 
		$amis = $em->getRepository('PortailBundle:Relation')->findBy(
            array('star' => $me, 'category' => $amitie, 'isActive' => true)
        );
        $amisAsked = $em->getRepository('PortailBundle:Relation')->findBy(
            array('follower' => $me, 'category' => $amitie, 'isActive' => true)
        ); 
        $ids = array();
        foreach ($amis as $relation) {
            $ids[] = $relation->getFollower()->getId();
        }
        foreach ($amisAsked as $relation) {
            $ids[] = $relation->getStar()->getId();
        }
		$qb->select('u')
            ->from('PortailBundle:User', 'u')
            ->where($qb->expr()->in('u.id', '?1'))
			->setParameter('1', $ids);
		if ($me->getGender() == '7' or $me->getCategory()->getNom() == 'Amateur' ) {
			 
			$qbc = $em->createQueryBuilder();
                $qbc->select('c')
                    ->from('PortailBundle:CCompte', 'c')
                    ->where($qbc->expr()->in('c.nom', '?1'))
                    ->setParameter('1', array('Semi Pro', 'Amateur' ));
                $categoryVal = $qbc->getQuery()->getResult();

                $qb->andWhere(
                    $qb->expr()->eq('u.category', $categoryVal["0"]->getId()  ) . ' OR '.
                    $qb->expr()->eq('u.category', $categoryVal["1"]->getId() ));
		}
        $qb->add('orderBy', 'u.username ASC');
            
        $activeFriends = $qb->getQuery()->getResult();
		

        return $this->container->get('templating')->renderResponse('PortailBundle:Message:newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData(),
            'allusers' => $activeFriends,
            'user' => $me,
            'tabselected' => ''
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
    /**
     * Deletes a thread
     *
     * @param string $slug the thread slug
     *
     * @return RedirectResponse
     */
    public function deleteAction($threadId, $slug)
    {
        $thread = $this->getProvider()->getThread($threadId);

        $this->container->get('fos_message.deleter')->markAsDeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);


        return new RedirectResponse($this->container->get('router')->generate('ltm_message_inbox'));
    }



    /**
     * Undeletes a thread
     *
     * @param string $slug
     *
     * @return RedirectResponse
     */
    public function undeleteAction($threadId, $slug)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsUndeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);


        return new RedirectResponse($this->container->get('router')->generate('ltm_message_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->container->get('fos_message.search_query_factory')->createFromRequest();
        $threads = $this->container->get('fos_message.search_finder')->find($query);

        $em = $this->container->get('doctrine')->getManager();
        $me = $em->find('PortailBundle:User', $this->get('security.context')->getToken()->getUser()->getId());

        return $this->container->get('templating')->renderResponse('PortailBundle:Message:search.html.twig', array(
            'query' => $query,
            'threads' => $threads,
            'user' => $me,
            'tabselected' => ''
        ));
    }
	
    /**
     * Gets the provider service
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }

}