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
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
use BenGor\User\Domain\Model\UserRole;
use BenGor\User\Domain\Model\UserToken;
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
     * Factory method that builds object class from user.
     *
     * @param array $data Array which contains the user data
     *
     * @return static
     */
    public static function build(array $data)
    {
        new static(
            new UserId($data['id']),
            new UserEmail($data['email']),
            UserPassword::fromEncoded(
                $data['password'],
                $data['salt']
            ),
            array_map(function ($role) {
                return new UserRole($role);
            }, $data['roles']),
            $data['created_on'],
            $data['updated_on'],
            $data['last_login'],
            new UserToken($data['confirmation_token']),
            new UserToken($data['remember_password_token'])
        );
    }

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
