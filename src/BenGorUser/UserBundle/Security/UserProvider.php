<?php

namespace BenGorUser\UserBundle\Security;

use BenGorUser\UserBundle\Application\UserCommandBus;
use BenGorUser\UserBundle\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserProvider implements UserProviderInterface
{
    /**
     * The user command bus.
     *
     * @var UserCommandBus
     */
    private $userCommandBus;
    /**
     * @var RequestStack
     */
    private $request;

//    /**
//     * Constructor.
//     *
//     * @param UserCommandBus $aUserCommandBus The user command bus
//     */
//    public function __construct(UserCommandBus $aUserCommandBus)
//    {
//        $this->userCommandBus = $aUserCommandBus;
//    }

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function loadUserByUsername($username)
    {
        dump($this->request->getCurrentRequest());
        dump($this->request->getMasterRequest());
        dump($this->request->getParentRequest());
//        // make a call to your webservice here
//        $userData = ...
//        // pretend it returns an array on success, false if there is no user
//
//        if ($userData) {
//            $password = '...';
//
//            // ...
//
//            return new User($username, $password, $salt, $roles);
//        }
//
//        throw new UsernameNotFoundException(
//            sprintf('Username "%s" does not exist.', $username)
//        );

        return new User($username, '12', ['ROLE_USER']);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
