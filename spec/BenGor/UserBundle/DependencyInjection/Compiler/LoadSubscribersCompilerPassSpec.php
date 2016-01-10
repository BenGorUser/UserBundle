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

use BenGor\User\Domain\Model\Event\UserInvited;
use BenGor\User\Domain\Model\Event\UserRegistered;
use BenGor\User\Domain\Model\Event\UserRememberPasswordRequested;
use BenGor\User\Domain\Model\User;
use BenGor\UserBundle\DependencyInjection\Compiler\LoadSubscribersCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Spec file of load subscribers compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class LoadSubscribersCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LoadSubscribersCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->findDefinition(
            'bengor.user_bundle.event_listener.domain_event_publisher'
        )->shouldBeCalled()->willReturn($definition);
        $container->findTaggedServiceIds('bengor_user_subscriber')
            ->shouldBeCalled()->willReturn([
            ]);
        $definition->replaceArgument(0, Argument::type('array'))
            ->shouldBeCalled()->willReturn($definition);


        $this->process($container);
    }
}
