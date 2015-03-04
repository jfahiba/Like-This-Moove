<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/19/14
 * Time: 6:16 PM
 */

namespace LTM\PortailBundle\Entity;

use Spy\Timeline\Model\ActionComponentInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\TimelineInterface;
use Spy\TimelineBundle\Entity\Action as BaseAction;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline_action")
 */
class Action extends BaseAction
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="ActionComponent", mappedBy="action", cascade={"persist"})
     */
    protected $actionComponents;

    /**
     * @ORM\OneToMany(targetEntity="Timeline", mappedBy="action")
     */
    protected $timelines;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $verb;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $statusCurrent = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $statusWanted = self::STATUS_PUBLISHED;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $duplicateKey;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $duplicatePriority;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $duplicated = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

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
     * Set verb
     *
     * @param string $verb
     * @return Action
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * Get verb
     *
     * @return string 
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * Set statusCurrent
     *
     * @param string $statusCurrent
     * @return Action
     */
    public function setStatusCurrent($statusCurrent)
    {

        return parent::setStatusCurrent($statusCurrent);
    }

    /**
     * Get statusCurrent
     *
     * @return string 
     */
    public function getStatusCurrent()
    {
        return $this->statusCurrent;
    }

    /**
     * Set statusWanted
     *
     * @param string $statusWanted
     * @return Action
     */
    public function setStatusWanted($statusWanted)
    {
        return parent::setStatusWanted($statusWanted);
    }

    /**
     * Get statusWanted
     *
     * @return string 
     */
    public function getStatusWanted()
    {
        return $this->statusWanted;
    }

    /**
     * Set duplicateKey
     *
     * @param string $duplicateKey
     * @return Action
     */
    public function setDuplicateKey($duplicateKey)
    {
        $this->duplicateKey = $duplicateKey;

        return $this;
    }

    /**
     * Get duplicateKey
     *
     * @return string 
     */
    public function getDuplicateKey()
    {
        return $this->duplicateKey;
    }

    /**
     * Set duplicatePriority
     *
     * @param integer $duplicatePriority
     * @return Action
     */
    public function setDuplicatePriority($duplicatePriority)
    {
        $this->duplicatePriority = $duplicatePriority;

        return $this;
    }

    /**
     * Get duplicatePriority
     *
     * @return integer 
     */
    public function getDuplicatePriority()
    {
        return $this->duplicatePriority;
    }

    /**
     * Set duplicated
     *
     * @param boolean $duplicated
     * @return Action
     */
    public function setDuplicated($duplicated)
    {
        $this->duplicated = $duplicated;

        return $this;
    }

    /**
     * Get duplicated
     *
     * @return boolean 
     */
    public function getDuplicated()
    {
        return $this->duplicated;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Action
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
     * Add actionComponents
     *
     * @param \LTM\PortailBundle\Entity\ActionComponent $actionComponents
     * @return Action
     */
    public function addActionComponent(ActionComponentInterface $actionComponents)
    {
        return parent::addActionComponent($actionComponents);
    }

    /**
     * Remove actionComponents
     *
     * @param \LTM\PortailBundle\Entity\ActionComponent $actionComponents
     */
    public function removeActionComponent(ActionComponentInterface $actionComponents)
    {
        $this->actionComponents->removeElement($actionComponents);
    }

    /**
     * Get actionComponents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActionComponents()
    {
        return $this->actionComponents;
    }

    /**
     * Add timelines
     *
     * @param \LTM\PortailBundle\Entity\Timeline $timelines
     * @return Action
     */
    public function addTimeline(TimeLineInterface $timelines)
    {

        return  parent::addTimeline($timelines);
    }

    /**
     * Remove timelines
     *
     * @param \LTM\PortailBundle\Entity\Timeline $timelines
     */
    public function removeTimeline(TimelineInterface $timelines)
    {
        $this->timelines->removeElement($timelines);
    }

    /**
     * Get timelines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTimelines()
    {
        return $this->timelines;
    }
}
