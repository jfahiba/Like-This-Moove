<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 28/07/2014
 * Time: 18:56
 */

namespace LTM\PortailBundle\Model;

use Payum\Core\Model\ArrayObject;

class PaymentDetails extends ArrayObject
{
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}