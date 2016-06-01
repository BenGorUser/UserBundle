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

use BenGorUser\User\Application\DataTransformer\UserDataTransformer;
use BenGorUser\User\Application\Query\UserOfEmailHandler;
use BenGorUser\User\Application\Query\UserOfEmailQuery;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
     * @var UserDataTransformer
     */
    private $dataTransformer;

    /**
     * The user of email query handler.
     *
     * @var UserOfEmailHandler
     */
    private $userOfEmailHandler;

    /**
     * Constructor.
     *
     * @param UserOfEmailHandler  $aUserOfEmailHandler The user of email query handler
     * @param UserDataTransformer $aDataTransformer    The user data transformer
     */
    public function __construct(UserOfEmailHandler $aUserOfEmailHandler, UserDataTransformer $aDataTransformer)
    {
        $this->dataTransformer = $aDataTransformer;
        $this->userOfEmailHandler = $aUserOfEmailHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        try {
            $user = $this->userOfEmailHandler->__invoke(
                new UserOfEmailQuery($username)
            );

            $this->dataTransformer->write($user);

            return $this->dataTransformer->read();

            // Catches any kind of exception that comes from domain or a service layer, and it throws
            // for Symfony's UserInterface, a suitable exception like "UsernameNotFoundException".
        } catch (UserDoesNotExistException $exception) {
            throw new UsernameNotFoundException();
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
