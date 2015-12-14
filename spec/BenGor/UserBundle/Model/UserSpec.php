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

namespace spec\BenGor\UserBundle\Model;

use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
use BenGor\User\Domain\Model\UserRole;
use BenGor\UserBundle\Model\User;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Spec file of bengor user class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class UserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new UserId(),
            new UserEmail('test@test.com'),
            UserPassword::fromEncoded('123456', 'dummy-salt'),
            [
                new UserRole('ROLE_USER'),
                new UserRole('ROLE_ADMIN'),
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(User::class);
    }

    function it_extends_user()
    {
        $this->shouldHaveType(User::class);
    }

    function it_implements_user()
    {
        $this->shouldHaveType(UserInterface::class);
    }

    function it_gets_roles()
    {
        $this->getRoles()->shouldReturn(['ROLE_USER', 'ROLE_ADMIN']);
    }

    function it_gets_password()
    {
        $this->getPassword()->shouldReturn('123456');
    }

    function it_gets_salt()
    {
        $this->getSalt()->shouldReturn('dummy-salt');
    }

    function it_gets_username()
    {
        $this->getUsername()->shouldReturn('test@test.com');
    }
}
