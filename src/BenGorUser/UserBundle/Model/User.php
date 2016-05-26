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

namespace BenGorUser\UserBundle\Model;

use BenGorUser\User\Domain\Model\UserRole;
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
class User implements UserInterface
{
    private $username;
    private $password;
    private $roles;

    public function __construct($username, $password, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }


//    /**
//     * {@inheritdoc}
//     */
//    public function getRoles()
//    {
//        return array_map(function (UserRole $role) {
//            return $role->role();
//        }, $this->roles());
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getPassword()
//    {
//        return $this->password()->encodedPassword();
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getSalt()
//    {
//        return $this->password()->salt();
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getUsername()
//    {
//        return $this->email()->email();
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function eraseCredentials()
//    {
//    }
}
