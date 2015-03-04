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
use Symfony\Component\Validator\Constraints\True;


class AuthenticationHandler extends ContainerAware implements AuthenticationSuccessHandlerInterface
{



    function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
      

        $user = $token->getUser();
        $user->setLastLoginDate(new \DateTime());
        $user->removeStatus($this->findStatus('Déconnecté'));
        $conn = $this->findStatus('Connecté');
        if (!$this->statusExists($conn, $user)) {
            $user->addStatus($conn);
        }

        $this->container->get('doctrine')->getManager()->flush();

        //$referer = $request->headers->get('referer');

        $request = $this->container->get('request');
        $session = $request->getSession();
        $referer = $session->get("login_referer");
        if (strpos($referer, 'profil/register') !== FALSE) {
            $aboRoute =  $session->get("abo_route");
            return new RedirectResponse($aboRoute);

        } elseif (strpos($referer, 'profil/ajouter') !== FALSE) {
            return new RedirectResponse(
                $this->container->get('router')->generate('ltm_payment_credit_pricing'));

        } else if (!$referer or strpos($referer, 'login') !== FALSE or strpos($referer, 'forgotpassword') !== FALSE){
            return new RedirectResponse(
                $this->container->get('router')->generate('ltm_usr_afficher'));
        }else {
            return new RedirectResponse($referer);
        }
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
