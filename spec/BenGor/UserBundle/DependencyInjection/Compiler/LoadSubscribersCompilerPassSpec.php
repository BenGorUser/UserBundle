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
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class'  => [
                'user' => [
                    'class' => User::class, 'firewall' => [
                        'name' => 'user', 'pattern' => '',
                    ],
                ],
            ],
            'subscribers' => [
                'invited_mailer'              => 'mandrill',
                'registered_mailer'           => 'mandrill',
                'remember_password_requested' => 'mandrill',
            ],
        ]);

        $container->getDefinition(
            'bengor.user.infrastructure.mailing.mandrill.user_mailer'
        )->shouldBeCalled();
        $container->getParameter('mailer_user')->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.domain.event.user_invited_mailer_subscriber', Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addTag(
            'bengor_user_subscriber', [
            'subscribes_to' => UserInvited::class,
        ])->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->setDefinition(
            'bengor.user.domain.event.user_registered_mailer_subscriber', Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addTag(
            'bengor_user_subscriber', [
            'subscribes_to' => UserRegistered::class,
        ])->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->setDefinition(
            'bengor.user.domain.event.user_remember_password_requested_subscriber', Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addTag(
            'bengor_user_subscriber', [
            'subscribes_to' => UserRememberPasswordRequested::class,
        ])->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);


        $this->process($container);
    }
}
