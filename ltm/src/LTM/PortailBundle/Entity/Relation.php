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
 * @ORM\Table(name="Relation")
 */
class Relation
{

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\CRelation")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\User")
     */
    private $star;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\User")
     */
    private $follower;

    /**
     * @ORM\Column(type="date")
     */
    private $creationDate;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;




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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Relation
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set category
     *
     * @param \LTM\PortailBundle\Entity\CRelation $category
     * @return Relation
     */
    public function setCategory(\LTM\PortailBundle\Entity\CRelation $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \LTM\PortailBundle\Entity\CRelation 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set star
     *
     * @param \LTM\PortailBundle\Entity\User $star
     * @return Relation
     */
    public function setStar(\LTM\PortailBundle\Entity\User $star = null)
    {
        $this->star = $star;

        return $this;
    }

    /**
     * Get star
     *
     * @return \LTM\PortailBundle\Entity\User 
     */
    public function getStar()
    {
        return $this->star;
    }

    /**
     * Set follower
     *
     * @param \LTM\PortailBundle\Entity\User $follower
     * @return Relation
     */
    public function setFollower(\LTM\PortailBundle\Entity\User $follower = null)
    {
        $this->follower = $follower;

        return $this;
    }

    /**
     * Get follower
     *
     * @return \LTM\PortailBundle\Entity\User 
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Relation
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
