<?php

/*
 * This file is part of the BenGorUserBundle bundle.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGor\UserBundle\Model;

use BenGor\User\Domain\Model\User as BenGorUser;
use BenGor\User\Domain\Model\UserRole;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User bundle class.
 *
 * Necessary extension of domain model user
 * that implements the Symfony security's user interface
 * to integrate the Symfony's firewall.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class User extends BenGorUser implements UserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return array_map(function (UserRole $role) {
            return $role->role();
        }, $this->roles());
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password()->encodedPassword();
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->password()->salt();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->email()->email();
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
