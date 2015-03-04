<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 27/07/2014
 * Time: 20:59
 */

namespace LTM\PortailBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

use LTM\PortailBundle\Model\PaymentDetails as BasePaymentDetails;

/**
 * @ORM\Table(name="payum_payment_details")
 * @ORM\Entity
 */
class PaymentDetails extends BasePaymentDetails
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
