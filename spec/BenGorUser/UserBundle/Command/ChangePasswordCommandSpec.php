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

namespace spec\BenGorUser\UserBundle\Command;

use BenGorUser\User\Application\Service\ChangePassword\ChangeUserPasswordRequest;
use BenGorUser\UserBundle\Command\ChangePasswordCommand;
use BenGorUser\UserBundle\Model\User;
use Ddd\Application\Service\TransactionalApplicationService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Spec file of ChangePasswordCommand class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordCommandSpec extends ObjectBehavior
{
    function let(TransactionalApplicationService $service)
    {
        $this->beConstructedWith($service, 'user', User::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChangePasswordCommand::class);
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
        $input->getArgument('email')->shouldBeCalled()->willReturn('user@user.com');
        $input->getArgument('password')->shouldBeCalled()->willReturn(123456);

        $input->hasArgument('command')->shouldBeCalled()->willReturn(true);
        $input->getArgument('command')->shouldBeCalled()->willReturn('command');
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled()->willReturn(true);
        $input->validate()->shouldBeCalled();

        $service->execute(Argument::type(ChangeUserPasswordRequest::class))
            ->shouldBeCalled()->willReturn(['email' => 'user@user.com']);

        $output->writeln(sprintf(
            'Changed password of <comment>%s</comment> %s', 'user@user.com', 'user'
        ))->shouldBeCalled();

        $this->run($input, $output);
    }
}
