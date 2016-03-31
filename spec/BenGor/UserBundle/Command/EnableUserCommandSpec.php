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

use BenGor\User\Application\Service\EnableUserRequest;
use BenGor\UserBundle\Command\EnableUserCommand;
use Ddd\Application\Service\TransactionalApplicationService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Spec file of enable user command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class EnableUserCommandSpec extends ObjectBehavior
{
    function let(TransactionalApplicationService $service)
    {
        $this->beConstructedWith($service, 'user');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EnableUserCommand::class);
    }

    function it_extends_command()
    {
        $this->shouldHaveType(Command::class);
    }

    function it_executes(InputInterface $input, OutputInterface $output, TransactionalApplicationService $service)
    {
        $service->execute(Argument::type(EnableUserRequest::class))->shouldBeCalled();
        $output->writeln(sprintf('Enabled <comment>%s</comment>', 'user'))->shouldBeCalled();

        $this->run($input, $output)->shouldReturn(0);
    }
}
