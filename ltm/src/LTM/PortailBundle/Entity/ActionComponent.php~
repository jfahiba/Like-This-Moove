<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/19/14
 * Time: 6:16 PM
 */

namespace LTM\PortailBundle\Entity;


use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\TimelineBundle\Entity\ActionComponent as BaseActionComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline_action_component")
 */
class ActionComponent extends BaseActionComponent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\Action", inversedBy="actionComponents")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $action;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\Component")
     * @ORM\JoinColumn(name="component_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $component;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $text;


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
     * Set type
     *
     * @param string $type
     * @return ActionComponent
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
     * Set text
     *
     * @param string $text
     * @return ActionComponent
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set action
     *
     * @param \LTM\PortailBundle\Entity\Action $action
     * @return ActionComponent
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
     * Set component
     *
     * @param \LTM\PortailBundle\Entity\Component $component
     * @return ActionComponent
     */
    public function setComponent(ComponentInterface $component = null)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Get component
     *
     * @return \LTM\PortailBundle\Entity\Component 
     */
    public function getComponent()
    {
        return $this->component;
    }
}
