<?php

namespace BenGorUser\UserBundle\Security;

use BenGorUser\User\Domain\Model\Exception\UserEmailInvalidException;
use BenGorUser\User\Domain\Model\UserEmail;
use BenGorUser\User\Domain\Model\UserRepository;
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
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function loadUserByUsername($username)
    {
        try {
            $user = $this->repository->userOfEmail(
                new UserEmail($username)
            );
            // Transform user to a DTO to disallow changing user outside domain
            // $user = UserToDTOTransformer->transform($user)

            return $user;
        } catch(UserEmailInvalidException $e) {
            return null;
        }
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
