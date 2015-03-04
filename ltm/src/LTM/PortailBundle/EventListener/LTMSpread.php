<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/19/14
 * Time: 6:50 PM
 */

namespace LTM\PortailBundle\EventListener;

use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\EntryUnaware;
use Symfony\Component\Security\Core\SecurityContext;


class LTMSpread implements SpreadInterface
{
    public function __construct(SecurityContext $security, $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;

    }

    public function supports(ActionInterface $action)
    {
        return true;
    }

    public function process(ActionInterface $action, EntryCollection $coll)
    {
        $em = $this->doctrine->getManager();
        $subject = $action->getComponent('subject');
        if ($subject) {
            $user = $em->find('PortailBundle:User', $subject->getIdentifier());
        }

        if (!$user)
        {
            return;
        }

        $amis = $em->getRepository('PortailBundle:Relation')->findBy(
            array('star' => $user, 'category' => $this->findRelationCategory('Amitie'), 'isActive' => true)
        );
        $amisAsked = $em->getRepository('PortailBundle:Relation')->findBy(
            array('follower' => $user, 'category' => $this->findRelationCategory('Amitie'), 'isActive' => true)
        );
        $followers = $em->getRepository('PortailBundle:Relation')->findBy(
            array('star' => $user, 'category' => $this->findRelationCategory('Fan'), 'isActive' => true)
        );

        $ids[] = array();
        foreach ($amis as $relation) {
            $ids[] = $relation->getFollower()->getId();
        }
        foreach ($amisAsked as $relation) {
            $ids[] = $relation->getStar()->getId();
        }
        foreach ($followers as $relation) {
            $ids[] = $relation->getFollower()->getId();
        }

        foreach ($ids as $idUser) {
            $coll->add(new EntryUnaware('\User', $idUser));
        }
    }

    private function findRelationCategory ($name){

        $relation = $this->doctrine
            ->getRepository('PortailBundle:CPublish')
            ->findOneBy(array('nom' => $name));

        if (!$relation) {
            throw $this->createNotFoundException(
                'No relation found for name '.$name
            );
        }
        return $relation;

    }
}