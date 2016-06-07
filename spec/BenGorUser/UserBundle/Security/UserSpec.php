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

namespace spec\BenGorUser\UserBundle\Security;

use BenGorUser\UserBundle\Security\User;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Spec file of User class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class UserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('test@test.com', '123456', ['ROLE_USER', 'ROLE_ADMIN'], 'dummy-salt');
    }

    function it_is_initializable()
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
        $this->__toString()->shouldReturn('test@test.com');
    }
}
