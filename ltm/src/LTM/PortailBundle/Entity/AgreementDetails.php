<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 28/07/2014
 * Time: 18:54
 */

namespace LTM\PortailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LTM\PortailBundle\Model\AgreementDetails as BaseAgreementDetails;

/**
 * @ORM\Table(name="payum_agreement_details")
 * @ORM\Entity
 */
class AgreementDetails extends BaseAgreementDetails
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
