<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/29/14
 * Time: 2:49 PM
 */

namespace LTM\PortailBundle\Handler;


use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Security\AuthorizerInterface;

class MessageAuthorizer implements AuthorizerInterface{
    /**
     * Tells if the current user is allowed
     * to see this thread
     *
     * @param ThreadInterface $thread
     * @return boolean
     */
    function canSeeThread(ThreadInterface $thread)
    {
        return true;

    }

    /**
     * Tells if the current participant is allowed
     * to delete this thread
     *
     * @param ThreadInterface $thread
     * @return boolean
     */
    function canDeleteThread(ThreadInterface $thread)
    {
        return true;

    }

    /**
     * Tells if the current participant is allowed
     * to send a message to this other participant
     *
     * @param ParticipantInterface $participant the one we want to send a message to
     * @return boolean
     */
    function canMessageParticipant(ParticipantInterface $participant)
    {
        return true;

    }

} 