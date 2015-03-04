<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/19/14
 * Time: 11:45 AM
 */

namespace LTM\PortailBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormTypeInterface;

class CreateUserFlow extends FormFlow {

    /**
     * @var FormTypeInterface
     */
    protected $formType;

    public function setFormType(FormTypeInterface $formType) {
        $this->formType = $formType;
    }

    public function getName() {
        return 'createUser';
    }

    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'Authentification',
                'type' => $this->formType,
            ),
            array(
                'label' => 'Identification',
                'type' => $this->formType,
            ),
            array(
                'label' => 'A Propos de moi',
                'type' => $this->formType,
            ),
            array(
                'label' => 'Confirmation',
                'type' => $this->formType,
            ),
        );
    }
}