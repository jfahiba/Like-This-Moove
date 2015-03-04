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

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserForm());
        $builder->add(
            'terms',
            'checkbox',
            array(
                'property_path' => 'termsAccepted',
                'label' => 'En cochant sur cette case, vous vous engagez à respecter les clauses d\'utilisation de LikeThisMoove')
        );
        $builder->add('Register', 'submit', array('label' => 'Soumettre'));
    }

    public function getName()
    {
        return 'registration';
    }
}