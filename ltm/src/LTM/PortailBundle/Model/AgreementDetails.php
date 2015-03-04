<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 28/07/2014
 * Time: 18:55
 */

namespace LTM\PortailBundle\Model;


use Payum\Core\Model\ArrayObject;

class AgreementDetails extends ArrayObject
{
    protected $id;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}