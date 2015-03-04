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

class FilmForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', 'text', array('label' => 'Titre du film'))
            ->add('description', 'textarea', array('label' => 'Description'))
            ->add('acteurs', 'entity', array(
                'class' => 'LTM\PortailBundle\Entity\Acteur',
                'property' => 'PrenomNom',
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ))->add('categorie', 'entity', array(
                'class' => 'LTM\PortailBundle\Entity\Categorie',
                'property' => 'nom',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ));

    }

    public function getName()
    {
        return 'film';
    }
}