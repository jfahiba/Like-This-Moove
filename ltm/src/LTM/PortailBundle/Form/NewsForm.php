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

class NewsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title', 'text', array('label' => 'Titre de la nouvelle', 'required' => true));

        $builder->add('description', 'textarea', array('label' => 'Description', 'required' => false));

        $builder->add('url', 'url', array('label' => 'Lien externe', 'required' => false));
        $builder->add('file','file',array('label' => 'Pièce jointe'));

        $builder->add('style', 'entity', array(
            'class' => 'LTM\PortailBundle\Entity\CDance',
            'label' => 'Styles de danse',
            'property' => 'nom',
            'expanded' => false,
            'multiple' => true,
            'required' => false
        ));

        $builder->add('category', 'entity', array(
            'class' => 'LTM\PortailBundle\Entity\CNews',
            'label' => 'Type de nouvelle',
            'property' => 'nom',
            'expanded' => false,
            'multiple' => false,
            'required' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LTM\PortailBundle\Entity\News'
        ));
    }


    public function getName()
    {
        return 'acteur';
    }
}