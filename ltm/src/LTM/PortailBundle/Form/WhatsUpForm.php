<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 26/06/2014
 * Time: 00:26
 */

namespace LTM\PortailBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WhatsUpForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', 'text', array('label' => 'Nom de famille'))
            ->add('prenom', 'text', array('label' => 'Prénom'))
            ->add('dateNaissance', 'birthday', array('label' => 'Date de naissance'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LTM\PortailBundle\Entity\WhatsUp'
        ));
    }


    public function getName()
    {
        return 'acteur';
    }
}