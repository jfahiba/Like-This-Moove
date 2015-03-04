<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 18/07/2014
 * Time: 20:55
 */

namespace LTM\PortailBundle\Twig\Extensions;


class LTMTwigSocialBar extends \Twig_Extension{

    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'ltm_social_bar';
    }

    public function getFunctions()
    {
        return array(
            'socialButtons' => new \Twig_Function_Method($this, 'getSocialButtons' ,array('is_safe' => array('html'))),
            'facebookButton' => new \Twig_Function_Method($this, 'getFacebookLikeButton' ,array('is_safe' => array('html'))),
            'twitterButton' => new \Twig_Function_Method($this, 'getTwitterButton' ,array('is_safe' => array('html'))),
            'googlePlusButton' => new \Twig_Function_Method($this, 'getGooglePlusButton' ,array('is_safe' => array('html'))),
        );
    }

    public function getSocialButtons($parameters = array())
    {
        // Aucun paramètre défini, on garde les valeurs par défaut
        if (!array_key_exists('facebook', $parameters)){
            $render_parameters['facebook'] = array();
            // des paramètres sont définis, on surcharge les valeurs
        }else if(is_array($parameters['facebook'])){
            $render_parameters['facebook'] = $parameters['facebook'];
            // le bouton n'est pas affiché
        }else{
            $render_parameters['facebook'] = false;
        }

        if (!array_key_exists('twitter', $parameters)){
            $render_parameters['twitter'] = array();
        }else if(is_array($parameters['twitter'])){
            $render_parameters['twitter'] = $parameters['twitter'];
        }else{
            $render_parameters['twitter'] = false;
        }

        if (!array_key_exists('googleplus', $parameters)){
            $render_parameters['googleplus'] = array();
        }else if(is_array($parameters['googleplus'])){
            $render_parameters['googleplus'] = $parameters['googleplus'];
        }else{
            $render_parameters['googleplus'] = false;
        }

        // récupère le service du helper et affiche le template
        return $this->container->get('ltm.socialBarHelper')->socialButtons($render_parameters);
    }

    // https://developers.facebook.com/docs/reference/plugins/like/
    public function getFacebookLikeButton($parameters = array())
    {
        // valeurs par défaut. Vous pouvez les surcharger en les définissant
        $parameters = $parameters + array(
                'url' => null,
                'locale' => 'en_US',
                'send' => false,
                'width' => 300,
                'showFaces' => false,
                'layout' => 'button_count',
            );

        return $this->container->get('ltm.socialBarHelper')->facebookButton($parameters);
    }

    public function getTwitterButton($parameters = array())
    {
        $parameters = $parameters + array(
                'url' => null,
                'locale' => 'en',
                'message' => 'I want to share that page with you',
                'text' => 'Tweet',
                'via' => 'The LTM team',
                'tag' => 'ttot',
            );


        return $this->container->get('ltm.socialBarHelper')->twitterButton($parameters);
    }

    public function getGooglePlusButton($parameters = array())
    {
        $parameters = $parameters + array(
                'url' => null,
                'locale' => 'en',
                'size' => 'medium',
                'annotation' => 'bubble',
                'width' => '300',
            );

        return $this->container->get('ltm.socialBarHelper')->googlePlusButton($parameters);
    }
}