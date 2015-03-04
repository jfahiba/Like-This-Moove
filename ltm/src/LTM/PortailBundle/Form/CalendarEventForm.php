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


class CalendarEventForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('allDay', 'checkbox', array('label' => 'Toute la journée', 'required' => false));
        $builder->add('start', 'datetime', array('label' => 'Date de début', 'required' => true));
        $builder->add('end', 'datetime' , array('label' => 'Date de fin'));
        $builder->add('title', 'text', array('label' => 'Titre de l\'évènement', 'required' => true));
        $builder->add('location', 'text', array('label' => 'Adresse', 'required' => false));
		$builder->add('city', 'text',array('label' => 'Ville', 'required' => false));
        $builder->add('country', 'country', array('label' => 'Pays', 'required' => false));
		
        $builder->add('description', 'textarea', array('label' => 'Description', 'required' => false));
        $builder->add('url', 'url', array('label' => 'Lien externe', 'required' => false));
        $builder->add('file','file',array('label' => 'Pièce jointe', 'required' => false));

        $builder->add('style', 'entity', array(
            'class' => 'LTM\PortailBundle\Entity\CDance',
            'label' => 'Styles de danse',
            'property' => 'nom',
            'expanded' => false,
            'multiple' => true,
            'required' => false
        ));

        $builder->add('category', 'entity', array(
            'class' => 'LTM\PortailBundle\Entity\CCalendarEvent',
            'label' => 'Type d\'évènement',
            'property' => 'nom',
            'expanded' => false,
            'multiple' => false,
            'required' => false
        ));


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LTM\PortailBundle\Entity\CalendarEvent'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}