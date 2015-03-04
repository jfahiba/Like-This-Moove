<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/19/14
 * Time: 6:14 PM
 */

namespace LTM\PortailBundle\Entity;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\TimelineBundle\Entity\Timeline as BaseTimeline;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline")
 */
class Timeline extends BaseTimeline
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\Action")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     */
    protected $action;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\Component")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $subject;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    protected $context;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    protected $type = self::TYPE_TIMELINE;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;



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
     * Set context
     *
     * @param string $context
     * @return Timeline
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string 
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Timeline
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Timeline
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set action
     *
     * @param \LTM\PortailBundle\Entity\Action $action
     * @return Timeline
     */
    public function setAction(ActionInterface $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \LTM\PortailBundle\Entity\Action 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set subject
     *
     * @param \LTM\PortailBundle\Entity\Component $subject
     * @return Timeline
     */
    public function setSubject(ComponentInterface $subject = null)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return \LTM\PortailBundle\Entity\Component 
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
