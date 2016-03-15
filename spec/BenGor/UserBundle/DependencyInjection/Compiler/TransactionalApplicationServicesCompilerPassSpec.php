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
use BenGor\UserBundle\DependencyInjection\Compiler\TransactionalApplicationServicesCompilerPass;
use Ddd\Application\Service\TransactionalApplicationService;
use Ddd\Infrastructure\Application\Service\DoctrineSession;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Spec file of transactional application services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class TransactionalApplicationServicesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TransactionalApplicationServicesCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class'       => User::class,
                    'firewall'    => 'main',
                    'persistence' => 'doctrine',
                ],
            ],
        ]);

        $container->register(
            'bengor.user.infrastructure.application.service.doctrine_session',
            DoctrineSession::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.activate_user_account_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.change_user_password_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.change_user_password_using_remember_password_token_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.invite_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.log_in_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.log_out_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.remove_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.request_user_remember_password_token_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.sign_up_user_by_invitation_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->register(
            'bengor.user.application.service.sign_up_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(Argument::type(Reference::class))->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
