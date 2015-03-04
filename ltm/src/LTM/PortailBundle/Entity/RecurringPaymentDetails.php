<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 28/07/2014
 * Time: 18:51
 */

namespace LTM\PortailBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use LTM\PortailBundle\Model\RecurringPaymentDetails as BaseRecurringPaymentDetails;

/**
 * @ORM\Table(name="payum_recurring_payment_details")
 * @ORM\Entity
 */
class RecurringPaymentDetails extends BaseRecurringPaymentDetails
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
