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

class AdvertisementForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title', 'text', array('label' => 'Titre de l\'annonce', 'required' => true));
        $builder->add('url', 'url', array('label' => 'Url externe de l\'annonce', 'required' => false));
        $builder->add('file','file',array('label' => 'Charger l\'image de l\'annonce', 'required' => false));

        $builder->add('description', 'textarea', array('label' => 'Description de l\'annonce', 'required' => false)); 
		
		$builder->add('city', 'text',array('label' => 'Ville', 'required' => false));
		$builder->add('country', 'country', array('label' => 'Pays', 'required' => false));
		
		$builder->add('proUntil', 'date', array(
                    'label' => ' ',
                    'input' => 'datetime',
                    'required' => false, ));
		
        /*$builder->add('style', 'entity', array(
            'class' => 'LTM\PortailBundle\Entity\CDance',
            'label' => 'Styles de danse',
            'property' => 'nom',
            'expanded' => false,
            'multiple' => true,
            'required' => false
        ));*/
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LTM\PortailBundle\Entity\Advertisement'
        ));
    }


    public function getName()
    {
        return 'advertisement';
    }
}