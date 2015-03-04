<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/19/14
 * Time: 6:16 PM
 */

namespace LTM\PortailBundle\Entity;

use Spy\TimelineBundle\Entity\Component as BaseComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline_component")
 */
class Component extends BaseComponent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $model;

    /**
     * @ORM\Column(type="array", length=255, nullable=true)
     */
    protected $identifier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $hash;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    protected $data;



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
     * Set model
     *
     * @param string $model
     * @return Component
     */
    public function setModel($model)
    {


        return parent::setModel($model);
    }

    /**
     * Get model
     *
     * @return string 
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set identifier
     *
     * @param array $identifier
     * @return Component
     */
    public function setIdentifier($identifier)
    {

        return parent::setIdentifier($identifier);
    }

    /**
     * Get identifier
     *
     * @return array 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Component
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return Component
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }
}
