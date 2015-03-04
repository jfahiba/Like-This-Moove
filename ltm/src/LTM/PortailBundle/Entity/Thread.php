<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/29/14
 * Time: 2:21 PM
 */

namespace LTM\PortailBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Entity\Thread as BaseThread;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
class Thread extends BaseThread
{
    use ORMBehaviors\Sluggable\Sluggable ;


    public function getSluggableFields()
    {
        return [ 'createdBy', 'subject' ];
    }


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;



    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\User")
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    protected $createdBy;

    /**
     * @ORM\OneToMany(
     *   targetEntity="LTM\PortailBundle\Entity\Message",
     *   mappedBy="thread"
     * )
     * @var Message[]|\Doctrine\Common\Collections\Collection
     */
    protected $messages;

    /**
     * @ORM\OneToMany(
     *   targetEntity="LTM\PortailBundle\Entity\ThreadMetadata",
     *   mappedBy="thread",
     *   cascade={"all"}
     * )
     * @var ThreadMetadata[]|\Doctrine\Common\Collections\Collection
     */
    protected $metadata;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subject;

    /**
     * @ORM\Column(name="isSpam", type="boolean")
     */
    protected $isSpam = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->metadata = new ArrayCollection();
        $this->participants = new ArrayCollection();
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
     * Set createdBy
     *
     * @param \LTM\PortailBundle\Entity\User $createdBy
     * @return Thread
     */
    public function setCreatedBy(ParticipantInterface $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \LTM\PortailBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Add messages
     *
     * @param \LTM\PortailBundle\Entity\Message $messages
     * @return Thread
     */
    public function addMessage(MessageInterface $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \LTM\PortailBundle\Entity\Message $messages
     */
    public function removeMessage(\LTM\PortailBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add metadata
     *
     * @param \LTM\PortailBundle\Entity\ThreadMetadata $metadata
     * @return Thread
     */
    public function addMetadatum(\LTM\PortailBundle\Entity\ThreadMetadata $metadata)
    {
        $this->metadata[] = $metadata;

        return $this;
    }

    /**
     * Remove metadata
     *
     * @param \LTM\PortailBundle\Entity\ThreadMetadata $metadata
     */
    public function removeMetadatum(\LTM\PortailBundle\Entity\ThreadMetadata $metadata)
    {
        $this->metadata->removeElement($metadata);
    }

    /**
     * Get metadata
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Thread
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
     * Set subject
     *
     * @param string $subject
     * @return Thread
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set isSpam
     *
     * @param boolean $isSpam
     * @return Thread
     */
    public function setIsSpam($isSpam)
    {
        $this->isSpam = $isSpam;

        return $this;
    }

    /**
     * Get isSpam
     *
     * @return boolean 
     */
    public function getIsSpam()
    {
        return $this->isSpam;
    }
}
