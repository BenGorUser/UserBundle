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

use BenGorUser\User\Application\Command\PurgeOutdatedTokens\PurgeOutdatedInvitationTokensUserCommand;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Command\PurgeOutdatedInvitationTokensCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Spec file of PurgeOutdatedInvitationTokensCommand class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class PurgeOutdatedInvitationTokensCommandSpec extends ObjectBehavior
{
    function let(UserCommandBus $commandBus)
    {
        $this->beConstructedWith($commandBus, 'user');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PurgeOutdatedInvitationTokensCommand::class);
    }

    function it_extends_command()
    {
        $this->shouldHaveType(Command::class);
    }

    function it_executes(InputInterface $input, OutputInterface $output, UserCommandBus $commandBus)
    {
        $commandBus->handle(Argument::type(PurgeOutdatedInvitationTokensUserCommand::class))->shouldBeCalled();

        $output->writeln(sprintf('Purged outdated invitation tokens of %s', 'user'))->shouldBeCalled();

        $this->run($input, $output);
    }
}
