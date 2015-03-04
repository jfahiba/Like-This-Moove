<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 10/07/2014
 * Time: 18:54
 */

namespace LTM\PortailBundle\Handler;


use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;


class LogoutHandler extends ContainerAware implements LogoutSuccessHandlerInterface
{

    private $security;

    public function __construct(SecurityContext $security) {
        $this->security = $security;
    }


    public function getSecurity() {
        return $this->security;
    }
    public function onLogoutSuccess(Request $request)
    {

        //$referer = $request->headers->get('referer');

        $user = $this->security->getToken()->getUser();

        $user->setLastLoginDate(new \DateTime());
        $user->removeStatus($this->findStatus('Connecté'));

        $deconn = $this->findStatus('Déconnecté');
        if (!$this->statusExists($deconn, $user)) {
            $user->addStatus($deconn);
        }
        $this->container->get('doctrine')->getManager()->flush();

        return new RedirectResponse($this->container->get('router')->generate('home'));


        //return new RedirectResponse($referer);
    }

    private function findStatus ($name){

        $status = $this->container->get('doctrine')
            ->getRepository('PortailBundle:CStatus')
            ->findOneBy(array('nom' => $name));

        if (!$status) {
            throw $this->createNotFoundException(
                'No status found for name '.$name
            );
        }
        return $status;

    }

    private function statusExists ($status, $user){

        $userStatus = $user->getStatus();
        $searchedStatusId = $status->getId();
        for ($i = 0; $i < count($userStatus); $i++ ){

            if ($searchedStatusId == $userStatus[$i]->getId()) {
                return true;
            }

        }
        return false;
    }

    private function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

}
