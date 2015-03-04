<?php

namespace LTM\PortailBundle\Menu;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class MenuBuilder extends Controller
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav top-2');

        $menu->addChild($this->get('translator')->trans("home"), array('route' => 'home'));
            //->setAttribute('dropdown', true);

        $em = $this->container->get('doctrine')->getManager();

        /*$qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('PortailBundle:CDance', 's');
		$qb->setMaxResults(6);
		$allstyles = $qb->getQuery()->getResult();*/
 
		$qbe = $em->createQueryBuilder();
        $qbe->select('c')
            ->from('PortailBundle:CCalendarEvent', 'c');
		$qbe->setMaxResults(6);
		$allcategoryevents = $qbe->getQuery()->getResult();
		
		$menu2 = $menu->addChild($this->get('translator')->trans("dancing.schools"), array('route' => 'ltm_usr_lister_ecoles'))
            ->setAttribute('dropdown', false);
		
		
        $menu2 = $menu->addChild($this->get('translator')->trans("Moovers"), array('route' => 'ltm_usr_lister'))
            ->setAttribute('dropdown', false);


        /*foreach ($allstyles as $style) {
            $menu2->addChild($style->getNom(),
                array('route' => 'ltm_usr_styled',
                    'routeParameters' => array('styled' => str_replace(' ', '_', $style->getNom()))));

        }
        $menu2->addChild('Autres ...', array('route' => 'ltm_usr_lister'));*/

        $menu->addChild($this->get('translator')->trans("whats.up"), array('route' => 'ltm_advertisement_lister_toutes'))
            ->setAttribute('dropdown', false);

        $menu2 = $menu->addChild($this->get('translator')->trans("Events"), array('route' => 'ltm_calendarevent_lister'));
        $menu2->setAttribute('dropdown', true);

        foreach ($allcategoryevents as $categoryevent) {
            $menu2->addChild($categoryevent->getNom(),
                array('route' => 'ltm_calendarevent_categoryev',
                    'routeParameters' => array('categoryev' => str_replace(' ', '_', $categoryevent->getNom()))));

        }

        /*foreach ($allstyles as $style) {
            $menu2->addChild($style->getNom(),
                array('route' => 'ltm_calendarevent_styled',
                    'routeParameters' => array('styled' => str_replace(' ', '_', $style->getNom()))));

        }

        */

        /*;*/

        /*$menu->addChild('Blog', array('uri' => '#'))
            ->setAttribute('dropdown', true);
        $menu['Blog']->addChild('Tous les blogs', array('route' => 'home'));
        $menu['Blog']->addChild('Le blog de la semaine', array('route' => 'home'));*/
        $menu2->addChild($this->get('translator')->trans("Others") . ' ...', array('route' => 'ltm_calendarevent_lister'));
		$context = $this->get('security.context');
		if ($context->getToken()->getUser() != 'anon.'
		and $context->getToken()->getUser()->getGender() != '7'
		and $context->getToken()->getUser()->getCategory()->getNom() != 'Amateur') {
			$menu2->addChild($this->get('translator')->trans("event.add") . '...', array('route' => 'ltm_calendarevent_ajouter'));
		}
        $menu2 = $menu->addChild($this->get('translator')->trans("Videos"), array('route' => 'ltm_video_lister'))
            ->setAttribute('dropdown', false);


       /* foreach ($allstyles as $style) {
            $menu2->addChild($style->getNom(),
                array('route' => 'ltm_video_styled',
                    'routeParameters' => array('styled' => str_replace(' ', '_', $style->getNom()))));

        }
        $menu2->addChild('Plus...', array('route' => 'ltm_video_lister'));*/


        $menu->addChild($this->get('translator')->trans("tutos"), array('route' => 'home_tuto'))
            ->setAttribute('dropdown', false);

        $menu2 = $menu->addChild($this->get('translator')->trans("AboutMenu"), array('uri' => ''))
            ->setAttribute('dropdown', true);

        $menu2->addChild($this->get('translator')->trans("Recrutment"), array('route' => 'recrutement'))
            ->setAttribute('dropdown', false);
        $menu2->addChild($this->get('translator')->trans("About"), array('route' => 'apropos'))
            ->setAttribute('dropdown', false);
        $menu2->addChild($this->get('translator')->trans("Contact"), array('route' => 'contact'))
            ->setAttribute('dropdown', false);
        $menu2->addChild($this->get('translator')->trans("Shop"), array('uri' => '/shop/index.php'))
            ->setAttribute('dropdown', false);



        $menu->addChild('', array('uri' => '#'))
            ->setAttribute('icon', 'icon-search search-btn')
            ->setAttribute('class','search');



        return $menu;
    }

    public function userMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav pull-right');

        $menu->addChild('User')
            ->setAttribute('dropdown', true);

        $securityContext = $this->container->get('security.context');
        #if (!$securityContext->isGranted('ROLE_ADMIN')) {
            /*$menu->addChild('About Me', array(
                'route' => 'page_show',
                'routeParameters' => array('id' => 42)
            ));*/

            $menu['User']->addChild('Edit my Profile', array('uri' => '#'))
                ->setAttribute('divider_append', true)
                ->setAttribute('icon', 'icon-edit');
            $menu['User']->addChild('Logout', array('uri' => '#'));
        #}

        $menu->addChild('Language')
            ->setAttribute('dropdown', true)
            ->setAttribute('divider_prepend', true);

        $menu['Language']->addChild('Deutsch', array('uri' => '#'));
        $menu['Language']->addChild('English', array('uri' => '#'));
        /*
        You probably want to show user specific information such as the username here. That's possible! Use any of the below methods to do this.

        if($this->container->get('security.context')->isGranted(array('ROLE_ADMIN', 'ROLE_USER'))) {} // Check if the visitor has any authenticated roles
        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername(); // Get username of the current logged in user

        */



        return $menu;
    }

    public function tutoMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav top-2');

        $menu2 = $menu->addChild($this->get('translator')->trans("classes"), array('route' => 'ltm_video_tuto_lister'))
        ->setAttribute('dropdown', true);

        $em = $this->container->get('doctrine')->getManager();
 
		$qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('PortailBundle:CDance', 's');
		$qb->setMaxResults(6);
		$allstyles = $qb->getQuery()->getResult();
        foreach ($allstyles as $style) {
            $menu2->addChild($style->getNom(),
                array('route' => 'ltm_video_tuto_styled',
                      'routeParameters' => array('styled' => str_replace(' ', '_', $style->getNom()))));

        }
        $menu2->addChild($this->get('translator')->trans("Others") . ' ...', array('route' => 'ltm_video_tuto_lister'));
		$context = $this->get('security.context');
		if ($context->getToken()->getUser() != 'anon.'
		and $context->getToken()->getUser()->getGender() != '7'
		and $context->getToken()->getUser()->getCategory()->getNom() != 'Amateur') {
			$menu2->addChild($this->get('translator')->trans("tuto.add") . '...', array('route' => 'ltm_video_tuto_ajouter'));
		}
        /*$menu2 = $menu->addChild('Promotion', array('route' => 'home_tuto'))
        ->setAttribute('dropdown', true);
        $menu2->addChild('Top 10 du mois', array('route' => 'home_tuto'));
        $menu2->addChild("Top de l'année", array('route' => 'home_tuto'));*/


        $menu2 = $menu->addChild($this->get('translator')->trans("teachers"), array('route' => 'ltm_professeur_lister'))
            ->setAttribute('dropdown', true);

        foreach ($allstyles as $style) {
            $menu2->addChild($style->getNom(),
                array('route' => 'ltm_professeur_styled',
                    'routeParameters' => array('styled' => str_replace(' ', '_', $style->getNom()))));

        }
        $menu2->addChild($this->get('translator')->trans("Others") . '...', array('route' => 'ltm_professeur_lister'));

		/*if ($context->getToken()->getUser() == 'anon.') {
        $menu->addChild($this->get('translator')->trans("free.try"), array('route' => 'ltm_usr_ajouter'))
            ->setAttribute('dropdown', false);
		}*/
        $menu->addChild($this->get('translator')->trans("back.to.ltm"), array('route' => 'home'))
            ->setAttribute('dropdown', false);
		if ($context->getToken()->getUser() == 'anon.') {
			$menu->addChild($this->get('translator')->trans("login"), array('route' => 'home_tuto'))
				->setAttribute('dropdown', false);
		}
        $menu->addChild('', array('uri' => '#'))
            ->setAttribute('icon', 'icon-search search-btn')
            ->setAttribute('class','search');

        return $menu;
    }

}