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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Spec file of register services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegisterServicesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('BenGor\UserBundle\DependencyInjection\Compiler\RegisterServicesCompilerPass');
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes(ContainerBuilder $container)
    {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => ['user' => 'BenGor\Domain\Model\User'],
        ]);

        $container->getDefinition('user_password_encoder')->shouldBeCalled();
        $container->getDefinition('doctrine.orm.default_entity_manager')->shouldBeCalled();
        $container->getDefinition('bengor.user.infrastructure.persistence.doctrine.user_repository')->shouldBeCalled();
        $container->getDefinition(
            'bengor.user.infrastructure.security.symfony.user_password_encoder'
        )->shouldBeCalled();
        $container->getDefinition(
            'bengor.user.infrastructure.domain.model.user_factory'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.infrastructure.persistence.in_memory.user_repository',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.infrastructure.persistence.in_memory.user_guest_repository',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'user_password_encoder',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.infrastructure.security.symfony.user_password_encoder',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.infrastructure.domain.model.user_factory',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.infrastructure.persistence.doctrine.user_repository',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.activate_user_account',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.change_user_password',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.change_user_password_using_remember_password_token',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.log_in_user',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.log_out_user',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.remove_user',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.request_user_remember_password_token',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.sign_up_user',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();

        $this->process($container);
    }
}
