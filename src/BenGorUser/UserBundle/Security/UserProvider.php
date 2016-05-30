<?php

/*
 * This file is part of the BenGorUser package.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGorUser\UserBundle\Security;

use BenGorUser\User\Domain\Model\Exception\UserEmailInvalidException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Custom user provider to obtain the domain user DTO
 * and converts to in a user DTO that implements UserInterface.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class UserProvider implements UserProviderInterface
{
    /**
     * Transforms given DTO to one
     * that implements UserInterface.
     *
     * @var UserInterfaceDataTransformer
     */
    private $dataTransformer;

    /**
     * The user of email query handler.
     *
     * @var UserOfEmailQueryHandler
     */
    private $userOfEmailQueryHandler;

    public function __construct(
        UserOfEmailQueryHandler $aUserOfEmailQueryHandler,
        UserInterfaceDataTransformer $aDataTransformer
    ) {
        $this->dataTransformer = $aDataTransformer;
        $this->userOfEmailQueryHandler = $aUserOfEmailQueryHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        try {
            $user = $this->userOfEmailQueryHandler->match(
                new UserOfEmailQueryCommand($username)
            );

            $this->dataTransformer->write($user);

            return $this->dataTransformer->read();
        } catch (UserEmailInvalidException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }
}
