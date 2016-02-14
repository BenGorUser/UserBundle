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

namespace spec\BenGor\UserBundle\DependencyInjection\Compiler;

use BenGor\User\Domain\Model\User;
use BenGor\UserBundle\DependencyInjection\Compiler\CommandsServicesCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of commands services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class CommandsServicesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CommandsServicesCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(
        ContainerBuilder $container,
        Definition $definition,
        Definition $signUpDefinition,
        Definition $activateAccountDefinition
    ) {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class' => User::class, 'firewall' => [
                        'name' => 'user', 'pattern' => '',
                    ],
                ],
            ],
        ]);

        $container->getDefinition('bengor.user.application.service.sign_up_user_doctrine_transactional')
            ->shouldBeCalled()->willReturn($signUpDefinition);
        $container->getDefinition('bengor.user.application.service.activate_user_account_doctrine_transactional')
            ->shouldBeCalled()->willReturn($activateAccountDefinition);

        $container->findDefinition('bengor.user_bundle.command.sign_up_user_command')
            ->shouldBeCalled()->willReturn($definition);
        $definition->setArguments(Argument::type('array'))->shouldBeCalled()->willReturn($definition);

        $container->findDefinition('bengor.user_bundle.command.activate_user_account_command')
            ->shouldBeCalled()->willReturn($definition);
        $definition->setArguments(Argument::type('array'))->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
