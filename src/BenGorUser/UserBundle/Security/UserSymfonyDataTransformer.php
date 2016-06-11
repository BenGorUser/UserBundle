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
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Data transformer that converts service layer returned
 * DTO, to DTO that implements Symfony's UserInterface.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class UserSymfonyDataTransformer implements UserDataTransformer
{
    /**
     * The service layer user DTO.
     *
     * @var mixed
     */
    private $user;

    /**
     * {@inheritdoc}
     */
    public function write($aUser)
    {
        if (!isset($aUser['email'], $aUser['roles'])) {
            throw new \InvalidArgumentException(
                'The user DTO must have at least keys of "email" and "roles"'
            );
        }
        $this->user = $aUser;
    }

    /**
     * {@inheritdoc}
     *
     * Builds an instance that implements UserInterface with required values.
     *
     * After that, populates dynamically the DTO with other
     * useful properties. To no repeat already uses properties,
     * unset items from array before iteration except email to
     * create custom an "email" field apart of "username".
     *
     * @return UserInterface
     */
    public function read()
    {
        $user = new User(
            $this->user['email'],
            $this->user['encoded_password'],
            $this->user['roles'],
            isset($this->user['salt']) ? $this->user['salt'] : null
        );

        unset($this->user['encoded_password']);
        unset($this->user['roles']);
        unset($this->user['salt']);

        foreach ($this->user as $property => $value) {
            $user->{lcfirst(implode(array_map('ucfirst', explode('_', $property))))} = $value;
        }

        return $user;
    }
}
