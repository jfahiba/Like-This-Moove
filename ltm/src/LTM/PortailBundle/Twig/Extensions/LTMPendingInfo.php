<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 18/07/2014
 * Time: 20:55
 */

namespace LTM\PortailBundle\Twig\Extensions;


class LTMPendingInfo extends \Twig_Extension{

    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container, $context)
    {
        $this->container = $container;
		$this->context = $context;
		
    }

    public function getName()
    {
        return 'ltm_pending_info';
    }

    public function getFunctions()
    {
        return array(
            'nbUnreadMessages' => new \Twig_Function_Method($this, 'getNbUnreadMessages' ,array('is_safe' => array('html'))),
            'pendingRelationAsk' => new \Twig_Function_Method($this, 'getPendingRelationAsk' ,array('is_safe' => array('html'))),
            'countTutos'  => new \Twig_Function_Method($this, 'countTutos' ,array('is_safe' => array('html'))),
            'alreadyRelation'  => new \Twig_Function_Method($this, 'isAlreadyRelation' ,array('is_safe' => array('html'))),
        );
    }
	
	public function getNbUnreadMessages()
    {
        
        return $this->container->get('fos_message.provider')->getNbUnreadMessages();
    }

    public function getPendingRelationAsk()
    {
		$em = $this->container->get('doctrine')->getManager();
        $me = $em->find('PortailBundle:User', $this->context->getToken()->getUser()->getId());
        $aValider = $em->getRepository('PortailBundle:Relation')->findBy(
            array('star' => $me,  'isActive' => false));

        return  count($aValider);
    }

    public function countTutos($parameters)
    {
        $em = $this->container->get('doctrine')->getManager();

        $user = $em->getRepository('PortailBundle:User')
            ->findOneBy($parameters);

        $videos= $em->getRepository('PortailBundle:VideoTuto')
            ->findBy(array('author' => $user->getId()));

        return  $videos == null? 0 : count($videos);
    }

    public function isAlreadyRelation($parameters)
    {
        $em = $this->container->get('doctrine')->getManager();

        $me = $em->find('PortailBundle:User', $this->context->getToken()->getUser()->getId());
        $category = $parameters['category'];
        $pairslug = $parameters['slug'];
        $pair = $em->getRepository('PortailBundle:User')
            ->findOneBy(array('slug'=>$pairslug));

        $relationCategory = $this->findRelationCategory($category);


        $relation= $em->getRepository('PortailBundle:Relation')->findOneBy(
            array('star' => $pair,
                'category' => $relationCategory,
                'follower' => $me)

        );
        $relationInverse = null;
        if ($category == 'Amitie') {
            $relationInverse = $em->getRepository('PortailBundle:Relation')->findOneBy(
                array('star' => $me,
                    'category' => $relationCategory,
                    'follower' => $pair)

            );
            if ($relation != null and $relation->getIsActive() or $relationInverse != null and $relationInverse->getIsActive()) {
                return 'yes';
            } elseif ($relation == null and $relationInverse == null){
                return 'no';
            } elseif ($relation == null){
                return 'pending-pair';
            } else {
                return 'pending-me';
            }
        } elseif ($category == 'Fan') {

            if ($relation != null) {
                return 'yes';
            } else {
                return 'no';
            }
        }
        else {
            if ($relation != null and $relation->getIsActive() ) {
                return 'yes';
            } elseif ($relation == null){
                return 'no';
            } else{
                return 'pending-pair';
            }
        }


    }

    private function findRelationCategory ($name){

        $relation = $this->container->get('doctrine')
            ->getRepository('PortailBundle:CPublish')
            ->findOneBy(array('nom' => $name));

        return $relation;

    }
   
}