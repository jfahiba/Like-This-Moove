<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 26/06/2014
 * Time: 00:26
 */

namespace LTM\PortailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Regex;


class UserForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $user  = $options['data'];
        $userOptions = $user->getOptions();
        $moovertype = $userOptions['moovertype'];
        $accounttype = $userOptions['accounttype'];
        $frequency = $userOptions['frequency'];
		$duration = $userOptions['duration'];

        switch ($options['flow_step']) {
            case 1:
                $builder->add('username', 'text', array(
                   'label' => 'Pseudo',
                   'constraints' => array(new Regex(array(
                       'pattern' => '/^[a-zA-Za0-9]+$/',

                       'message' => 'Pseudo invalide'
                   )))));
                $builder->add('plainPassword', 'repeated', array(

                    'first_name'  => 'Mot_de_Passe',
                    'second_name' => 'Confirmez',
                    'type'        => 'password',

                ));

                break;
            case 2:

                switch ($moovertype) {
                    case 'school':
                        $builder->add('lastName', 'text', array('label' => 'Nom', 'required' => false));
                        $builder->add('gender', 'choice',
                            array(
                                'choices'   =>
                                    array(
                                        '2' => 'Ecole',
                                        '3' => 'Association',

                                    ),
                                'label' => 'Vous etes un(e)', 'required' => true));
                        $builder->add('birthDate', 'birthday', array(
                            'label' => 'Date de creation',
                            'input' => 'datetime',
                            'required' => false, ));

                        break;
                    case 'recruiter':
                        $builder->add('lastName', 'text', array('label' => 'Nom', 'required' => false));
                        $builder->add('firstName', 'text', array('label' => 'Prénom', 'required' => false));
                        $builder->add('gender', 'choice',
                            array(
                                'choices'   =>
                                    array(
                                        '4' => 'Recruteur',
                                        '5' => 'Recruteuse',
                                        '6' => 'Agence de Recrutement'
                                    ),
                                'label' => 'Vous etes un(e)', 'required' => true));
                        $builder->add('birthDate', 'birthday', array(
                            'label' => 'Date de naissance/de creation',
                            'input' => 'datetime',
                            'required' => false, ));

                        break;
                    case 'dancer':
                        $builder->add('lastName', 'text', array('label' => 'Nom de famille', 'required' => false));
                        $builder->add('firstName', 'text', array('label' => 'Prénom', 'required' => false));
                        if ($accounttype == 'Visiteur') {
                            $builder->add('gender', 'choice',
                                array(
                                    'choices'   =>
                                        array(
                                            '7' => 'Visiteur'
                                        ),
                                    'label' => 'Vous etes un(e)', 'required' => true));

                        } else {
                            $builder->add('gender', 'choice',
                                array(
                                    'choices'   =>
                                        array(
                                            '0' => 'Danseur',
                                            '1' => 'Danseuse',
                                        ),
                                    'label' => 'Vous etes un(e)', 'required' => true));

                        }

                        $builder->add('birthDate', 'birthday', array(
                            'label' => 'Date de naissance',
                            'input' => 'datetime',
                            'required' => false, ));
                        break;
                }

                $builder->add('email', 'email' , array('label' => 'Email', 'required' => true));
                $builder->add('country', 'country', array('label' => 'Pays', 'required' => true));
                break;
            case 3:

                $builder->add('aPropos', 'textarea', array('label' => 'A propos de moi', 'required' => false));
                $builder->add('style', 'entity', array(
                    'class' => 'LTM\PortailBundle\Entity\CDance',
                    'label' => 'Styles préférés',
                    'property' => 'nom',
                    'expanded' => false,
                    'multiple' => true,
                    'required' => false
                ));
                break;
            case 4:


                $builder->add('captcha', 'captcha', array(
                    'width' => 200,
                    'height' => 70,
                    'length' => 4
                ));


                $builder->add('file','file',array('label' => 'Image de profil'));

                $builder->add(
                    'terms',
                    'checkbox',
                    array(
                        'property_path' => 'termsAccepted',
                        'label' => 'En cochant sur cette case, vous vous engagez à respecter les clauses d\'utilisation de LikeThisMoove'
                         )
                );

                /*$builder->add('category', 'entity', array(
                    'class' => 'LTM\PortailBundle\Entity\CCompte',
                    'label' => 'Type de compte souhaité',
                    'required' => false,
                    'property' => 'nom',
                    'expanded' => false,
                    'multiple' => false,

                ));*/

                if (false) {
						
						$builder->add('city', 'text',array('label' => 'Ville', 'required' => false));
                        $builder->add('webSite', 'text', array('label' => 'Site Web', 'required' => false));
                        $builder->add('facebook', 'text', array('label' => 'Url Facebook', 'required' => false));
                        $builder->add('twitter', 'text', array('label' => 'Url Twitter', 'required' => false));

                }
                break;
        }
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LTM\PortailBundle\Entity\User'
        ));
    }


    public function getName()
    {
        return 'user';
    }
}