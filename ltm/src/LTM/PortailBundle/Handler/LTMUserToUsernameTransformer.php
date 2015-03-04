<?php
/**
 * Created by PhpStorm.
 * User: ralphjohnson
 * Date: 8/29/14
 * Time: 3:19 PM
 */

namespace LTM\PortailBundle\Handler;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class LTMUserToUsernameTransformer implements DataTransformerInterface
{

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Transforms a UserInterface instance into a username string.
     *
     * @param mixed $value UserInterface instance
     *
     * @return string Username
     *
     * @throws UnexpectedTypeException if the given value is not a UserInterface instance
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!$value instanceof UserInterface) {
            throw new UnexpectedTypeException($value, 'Symfony\Component\Security\Core\User\UserInterface');
        }
        return $value->getUsername();
    }
    /**
     * Transforms a username string into a UserInterface instance.
     *
     * @param string $value Username
     *
     * @return UserInterface the corresponding UserInterface instance
     *
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->em->getRepository('PortailBundle:User')
            ->findOneBy(array('username' => $value));
    }
}