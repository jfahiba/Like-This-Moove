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

class VideoForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('label' => 'Titre de la vidéo', 'required' => true));

        $builder->add('url', 'url', array('label' => 'Url youtube ou Vimeo', 'required' => false));
        $builder->add('file','file',array('label' => 'Charger une video', 'required' => false));
        $builder->add('fileImage','file',array('label' => 'Image de preview', 'required' => false));

        $builder->add('description', 'textarea', array('label' => 'Description de la vidéo', 'required' => false));

        $builder->add('style', 'entity', array(
            'class' => 'LTM\PortailBundle\Entity\CDance',
            'label' => 'Styles de danse',
            'property' => 'nom',
            'expanded' => false,
            'multiple' => true,
            'required' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LTM\PortailBundle\Entity\Video'
        ));
    }


    public function getName()
    {
        return 'video';
    }
}