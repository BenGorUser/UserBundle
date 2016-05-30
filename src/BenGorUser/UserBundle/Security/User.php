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

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Bridge between user and Symfony that implements UserInterface.
 *
 * DTO that joins the user domain model
 * with Symfony's security firewall.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class User implements UserInterface
{
    /**
     * The username or email.
     *
     * @var string
     */
    private $username;

    /**
     * The password.
     *
     * @var string
     */
    private $password;

    /**
     * Array which contains the roles.
     *
     * @var array
     */
    private $roles;

    /**
     * The password salt.
     *
     * If the encryption mechanism is BCrypt
     * is not needed, so it can be null.
     *
     * @var string|null
     */
    private $salt;

    /**
     * Constructor.
     *
     * @param string      $aUsername The username or email
     * @param string      $aPassword The password
     * @param array       $roles     Array which contains roles
     * @param string|null $aSalt     The password salt
     */
    public function __construct($aUsername, $aPassword, array $roles, $aSalt = null)
    {
        $this->username = $aUsername;
        $this->password = $aPassword;
        $this->roles = $roles;
        $this->salt = $aSalt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * Magic method that represents this DTO in string format.
     */
    public function __toString()
    {
        return (string)$this->getUsername();
    }
}
