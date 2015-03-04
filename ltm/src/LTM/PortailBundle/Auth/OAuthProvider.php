<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 11/07/2014
 * Time: 20:17
 */

namespace LTM\PortailBundle\Auth;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use LTM\PortailBundle\Entity\User;



class OAuthProvider extends OAuthUserProvider
{
    protected $session, $doctrine, $admins;
    public function __construct($session, $doctrine, $service_container)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }
    public function loadUserByUsername($username)
    {
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('PortailBundle:User', 'u')
            ->where('u.facebookId = :fid')
            ->setParameter('fid', $username)
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();
        if (count($result)) {
            return $result[0];
        } else {
            return new User();
        }
    }
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        //Data from facebook response
        $facebook_id = $response->getUsername(); /* An ID like: 112259658235204980084 */
        $email = $response->getEmail();
        $nickname = $response->getNickname();
        $realname = $response->getRealName();
        $avatar = $response->getProfilePicture();
        var_dump($facebook_id, $email, $nickname, $realname, $avatar);
        //username,first_name,last_name,location.name,hometown.name,verified,gender,name,email,link,birthday,picture.type(square)
        //set data in session
        $this->session->set('email', $email);
        $this->session->set('nickname', $nickname);
        $this->session->set('realname', $realname);
        $this->session->set('avatar', $avatar);
        //Check if this facebook user already exists in our app DB
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('PortailBundle:User', 'u')
            ->where('u.facebookId = :fid')
            ->setParameter('fid', $facebook_id)
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();
        //add to database if doesn't exists
        if (!count($result)) {
            $user = new User();
            $user->setUsername($facebook_id);
            $user->setLastName($realname);
            $user->setFirstName($nickname);
            $user->setEmail($email);
            $user->setfacebookId($facebook_id);
            $user->setRolesStr('ROLE_USER');
            //Set some wild random pass since its irrelevant, this is facebook login
            //$factory = $this->container->get('security.encoder_factory');
            //$encoder = $factory->getEncoder($user);
            $password = uniqid();//$encoder->encodePassword(md5(uniqid()), $user->getSalt());
            $user->setPlainPassword($password);
            $em = $this->doctrine->getManager();
            //set profile image
            $em->persist($user);
            $em->flush();
        } else {
            $user = $result[0]; /* return User */
        }
        //set id
        $this->session->set('id', $user->getId());
        return $this->loadUserByUsername($response->getUsername());
    }
    public function supportsClass($class)
    {
        return $class === 'LTM\\PortailBundle\\Entity\\User';
    }
}