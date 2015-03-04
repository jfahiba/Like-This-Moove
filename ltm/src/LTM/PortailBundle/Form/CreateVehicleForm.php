<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/19/14
 * Time: 11:51 AM
 */

namespace LTM\PortailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateVehicleForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        switch ($options['flow_step']) {
            case 1:
                $validValues = array(2, 4);
                $builder->add('numberOfWheels', 'choice', array(
                    'choices' => array_combine($validValues, $validValues),
                    'empty_value' => '',
                ));
                break;
            case 2:
                $builder->add('engine', 'form_type_vehicleEngine', array(
                    'empty_value' => '',
                ));
                break;
        }
    }

    public function getName() {
        return 'createVehicle';
    }

}