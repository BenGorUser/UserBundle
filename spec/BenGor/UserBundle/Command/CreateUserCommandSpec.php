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

namespace spec\BenGor\UserBundle\Command;

use BenGor\User\Domain\Model\UserFactory;
use BenGor\User\Domain\Model\UserPasswordEncoder;
use BenGor\User\Domain\Model\UserRepository;
use BenGor\UserBundle\Command\CreateUserCommand;
use BenGor\UserBundle\Model\User;
use Ddd\Application\Service\TransactionalSession;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Command\Command;

/**
 * Spec file of create user command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class CreateUserCommandSpec extends ObjectBehavior
{
    function let(
        UserRepository $repository,
        UserPasswordEncoder $encoder,
        UserFactory $factory,
        TransactionalSession $session
    ) {
        $this->beConstructedWith($repository, $encoder, $factory, $session, 'user', User::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateUserCommand::class);
    }

    function it_extends_command()
    {
        $this->shouldHaveType(Command::class);
    }
}
