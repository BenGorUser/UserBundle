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

namespace spec\BenGorUser\UserBundle\Command;

use BenGorUser\User\Application\Command\SignUp\SignUpUserCommand;
use BenGorUser\User\Domain\Model\User;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Command\CreateUserCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Spec file of CreateUserCommand class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class CreateUserCommandSpec extends ObjectBehavior
{
    function let(UserCommandBus $commandBus)
    {
        $this->beConstructedWith($commandBus, 'user', User::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateUserCommand::class);
    }

    function it_extends_command()
    {
        $this->shouldHaveType(Command::class);
    }

    function it_executes(
        InputInterface $input,
        OutputInterface $output,
        UserCommandBus $commandBus
    ) {
        $input->getArgument('email')->shouldBeCalled()->willReturn('user@user.com');
        $input->getArgument('password')->shouldBeCalled()->willReturn(123456);
        $input->getArgument('roles')->shouldBeCalled()->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $input->hasArgument('command')->shouldBeCalled()->willReturn(true);
        $input->getArgument('command')->shouldBeCalled()->willReturn('command');
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled()->willReturn(true);
        $input->validate()->shouldBeCalled();

        $commandBus->handle(Argument::type(SignUpUserCommand::class))->shouldBeCalled();

        $output->writeln(sprintf(
            'Created %s: <comment>%s</comment>', 'user', 'user@user.com'
        ))->shouldBeCalled();

        $this->run($input, $output);
    }
}
