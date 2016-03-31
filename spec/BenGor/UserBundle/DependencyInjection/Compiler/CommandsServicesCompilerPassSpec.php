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
        Definition $userRepositoryDefinition,
        Definition $userPasswordEncoderDefinition,
        Definition $userFactoryDefinition,
        Definition $doctrineSession
    ) {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class'       => User::class,
                    'firewall'    => 'main',
                    'persistence' => 'doctrine',
                ],
            ],
        ]);

        $container->getDefinition('bengor.user.infrastructure.persistence.user_repository')
            ->shouldBeCalled()->willReturn($userRepositoryDefinition);
        $container->getDefinition('bengor.user.infrastructure.security.symfony.user_password_encoder')
            ->shouldBeCalled()->willReturn($userPasswordEncoderDefinition);
        $container->getDefinition('bengor.user.infrastructure.domain.model.user_factory')
            ->shouldBeCalled()->willReturn($userFactoryDefinition);

        $container->getDefinition('bengor.user.infrastructure.application.service.doctrine_session')
            ->shouldBeCalled()->willReturn($doctrineSession);

        $container->findDefinition('bengor.user_bundle.command.create_user_command')
            ->shouldBeCalled()->willReturn($definition);
        $definition->setArguments(Argument::type('array'))->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
