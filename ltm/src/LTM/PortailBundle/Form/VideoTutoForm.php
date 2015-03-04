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
use Symfony\Component\Validator\Constraints\Range;

class VideoTutoForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('label' => 'Titre de la vidéo', 'required' => true));
        $builder->add('url', 'url', array('label' => 'Url youtube / vimeo', 'required' => false));
        $builder->add('file','file',array('label' => 'Charger la video', 'required' => false));
		$builder->add('fileExtrait','file',array('label' => 'Charger un extrait du tuto', 'required' => false));

        $builder->add('fileImage','file',array('label' => 'Image de preview', 'required' => false));

        $builder
        ->add('price', 'choice',
        array(
            'choices'   => array('0' => '0','1' => '1','2' => '2','3' => '4','5' => '5','6' => '6','7' => '7',
                                 '8' => '8',  '9' => '9','10' => '10','15' => '15',
                                 '20' => '20','30' => '30', '40' => '40','50' => '50','60' => '60','70' => '70',
                                 '80' => '80', '90' => '90', '100' => '100'
                                ), 'label' => 'Montant', 'required' => true));

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
            'data_class' => 'LTM\PortailBundle\Entity\VideoTuto'
        ));
    }


    public function getName()
    {
        return 'video';
    }

}