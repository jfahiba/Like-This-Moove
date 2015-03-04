<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 25/06/2014
 * Time: 23:31
 */

namespace LTM\PortailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Credit
{

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $balance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedDate;

    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\CreditHistoric", cascade={"all"})
     */
    private $credithistoric;


    public function __construct()
    {

        $this->modifiedDate = new \DateTime("now");
        $this->balance = 0;
        $this->credithistoric = new \Doctrine\Common\Collections\ArrayCollection();

    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     * @return Credit
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate
     *
     * @return \DateTime 
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    public function canBuy($amount)
    {
        return $this->balance - $amount >= 0;
    }

    public function increment($amount, $cause)
    {
        $this->balance += $amount;
        $historic = new CreditHistoric();
        $historic->setAmount($amount);
        $historic->setMovement($cause);
        $this->setModifiedDate(new \DateTime("now"));
        $this->addCredithistoric($historic);
    }

    public function decrement($amount, $cause)
    {
        $this->balance -= $amount;
        $historic = new CreditHistoric();
        $historic->setAmount($amount);
        $historic->setMovement($cause);
        $this->setModifiedDate(new \DateTime("now"));
        $this->addCredithistoric($historic);
    }


    /**
     * Set balance
     *
     * @param integer $balance
     * @return Credit
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return integer 
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Add credithistoric
     *
     * @param \LTM\PortailBundle\Entity\CreditHistoric $credithistoric
     * @return Credit
     */
    public function addCredithistoric(\LTM\PortailBundle\Entity\CreditHistoric $credithistoric)
    {
        $this->credithistoric[] = $credithistoric;

        return $this;
    }

    /**
     * Remove credithistoric
     *
     * @param \LTM\PortailBundle\Entity\CreditHistoric $credithistoric
     */
    public function removeCredithistoric(\LTM\PortailBundle\Entity\CreditHistoric $credithistoric)
    {
        $this->credithistoric->removeElement($credithistoric);
    }

    /**
     * Get credithistoric
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCredithistoric()
    {
        return $this->credithistoric;
    }
}
