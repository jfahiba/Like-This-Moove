<?php

namespace LTM\PortailBundle\Controller;

use Spy\Timeline\Model\TimelineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




class ActivityController extends Controller
{

    public function activityAction($slug)
    {

        $em = $this->container->get('doctrine')->getManager();

        try {
            $user = $em->getRepository('PortailBundle:User')
                ->findOneBy(array('slug' => $slug));

            if (!$user)
            {
                throw new NotFoundHttpException("Utilisateur non trouvé");
            }


            $actionManager   = $this->get('spy_timeline.action_manager');
            $timelineManager = $this->get('spy_timeline.timeline_manager');

            $subject       = $actionManager->findOrCreateComponent('\User', $user->getSlug());
            $timelines = $timelineManager->getTimeline($subject,  array('type' => 'notification', 'max_per_page' => 5));


            //$countEntries = $timelineManager->countKeys($subject);

            return $this->container->get('templating')->renderResponse('PortailBundle:Activity:activity.html.twig', array(
                'timelines' => $timelines,
                'user' => $user
            ));
        }catch (Exception $e) {
            throw new NotFoundHttpException("Historique non trouvé");
        }

    }

    public function userActivityDetailsAction ($slug, $avatar = false) {
        $em = $this->container->get('doctrine')->getManager();

        $user = $em->getRepository('PortailBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");
        }

        return $this->render('PortailBundle:Activity:userActivityDetails.html.twig',
            array( 'user' => $user, 'avatar' => $avatar));

    }

    public function supprimerAction($slug = null, $idAction)
    {

        $actionManager   = $this->get('spy_timeline.action_manager');
        $timelineManager = $this->get('spy_timeline.timeline_manager');

        $subject       = $actionManager->findOrCreateComponent('\User', $slug);

        $timelineManager->remove(
            $subject,
            $idAction ,
            array( 'type'    => 'notification' ));

        $timelineManager->flush();

        $request = $this->container->get('request');
        $referer = $request->headers->get('referer');
        if (!$referer){
            return new RedirectResponse($this->container->get('router')->generate('home'));
        }else {
            return new RedirectResponse($referer);
        }
    }
}
