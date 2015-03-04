<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/29/14
 * Time: 2:22 PM
 */

namespace LTM\PortailBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;

/**
 * @ORM\Entity
 */
class ThreadMetadata extends BaseThreadMetadata
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *   targetEntity="LTM\PortailBundle\Entity\Thread",
     *   inversedBy="metadata"
     * )
     * @var \FOS\MessageBundle\Model\ThreadInterface
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\User")
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    protected $participant;

    /**
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted = false;


    /**
     * @ORM\Column(type="datetime",name="last_participant_message_date", nullable=true)
     */
    protected $lastParticipantMessageDate;
    /**
     * @ORM\Column(type="datetime", name="last_message_date", nullable=true)
     */
    protected $lastMessageDate;

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
     * Set thread
     *
     * @param \LTM\PortailBundle\Entity\Thread $thread
     * @return ThreadMetadata
     */
    public function setThread(ThreadInterface $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \LTM\PortailBundle\Entity\Thread 
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set participant
     *
     * @param \LTM\PortailBundle\Entity\User $participant
     * @return ThreadMetadata
     */
    public function setParticipant(ParticipantInterface $participant = null)
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * Get participant
     *
     * @return \LTM\PortailBundle\Entity\User 
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * Set lastParticipantMessageDate
     *
     * @param \DateTime $lastParticipantMessageDate
     * @return ThreadMetadata
     */
    public function setLastParticipantMessageDate(\DateTime $lastParticipantMessageDate)
    {
        $this->lastParticipantMessageDate = $lastParticipantMessageDate;

        return $this;
    }

    /**
     * Get lastParticipantMessageDate
     *
     * @return \DateTime 
     */
    public function getLastParticipantMessageDate()
    {
        return $this->lastParticipantMessageDate;
    }

    /**
     * Set lastMessageDate
     *
     * @param \DateTime $lastMessageDate
     * @return ThreadMetadata
     */
    public function setLastMessageDate(\DateTime $lastMessageDate)
    {
        $this->lastMessageDate = $lastMessageDate;

        return $this;
    }

    /**
     * Get lastMessageDate
     *
     * @return \DateTime 
     */
    public function getLastMessageDate()
    {
        return $this->lastMessageDate;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return ThreadMetadata
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean 
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
}
