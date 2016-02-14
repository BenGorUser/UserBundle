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

use BenGor\User\Application\Service\SignUpUserRequest;
use BenGor\User\Application\Service\SignUpUserResponse;
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
use BenGor\User\Domain\Model\UserRole;
use BenGor\UserBundle\Command\SignUpUserCommand;
use BenGor\UserBundle\Model\User;
use Ddd\Application\Service\TransactionalApplicationService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Spec file of sign up user command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpUserCommandSpec extends ObjectBehavior
{
    function let(TransactionalApplicationService $service)
    {
        $this->beConstructedWith($service, 'user', User::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SignUpUserCommand::class);
    }

    function it_extends_command()
    {
        $this->shouldHaveType(Command::class);
    }

    function it_executes(
        InputInterface $input,
        OutputInterface $output,
        TransactionalApplicationService $service
    ) {
        $user = new User(
            new UserId(),
            new UserEmail('benatespina@gmail.com'),
            UserPassword::fromEncoded('123456', 'dummy-salt'),
            [new UserRole('ROLE_USER'), new UserRole('ROLE_ADMIN')]
        );
        $response = new SignUpUserResponse($user);

        $input->getArgument('email')->shouldBeCalled()->willReturn('benatespina@gmail.com');
        $input->getArgument('password')->shouldBeCalled()->willReturn('123456');
        $input->getArgument('roles')->shouldBeCalled()->willReturn('ROLE_USER ROLE_ADMIN');
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled()->willReturn(true);
        $input->validate()->shouldBeCalled();
        $input->hasArgument('command')->shouldBeCalled()->willReturn(true);
        $input->getArgument('command')->shouldBeCalled()->willReturn('command');
        $input->getArgument('email')->shouldBeCalled()->willReturn('benatespina@gmail.com');
        $input->getArgument('password')->shouldBeCalled()->willReturn('123456');
        $input->getArgument('roles')->shouldBeCalled()->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $service->execute(Argument::type(SignUpUserRequest::class))->shouldBeCalled()->willReturn($response);

        $this->run($input, $output)->shouldReturn(0);
    }
}
