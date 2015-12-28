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
use BenGor\UserBundle\DependencyInjection\Compiler\ApplicationServicesCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of application services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationServicesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationServicesCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container)
    {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class' => User::class, 'firewall' => [
                        'name' => 'user', 'pattern' => '',
                    ],
                ],
            ],
        ]);

        $container->getDefinition('bengor.user.infrastructure.persistence.doctrine.user_repository')->shouldBeCalled();
        $container->getDefinition('bengor.user.infrastructure.persistence.doctrine.user_guest_repository')->shouldBeCalled();
        $container->getDefinition(
            'bengor.user.infrastructure.security.symfony.user_password_encoder'
        )->shouldBeCalled();
        $container->getDefinition(
            'bengor.user.infrastructure.domain.model.user_factory'
        )->shouldBeCalled();
        $container->getDefinition('router.default')->shouldBeCalled();
        $container->getDefinition('bengor.user.application.service.log_in_user')->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.service.activate_user_account',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.change_user_password',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.change_user_password_using_remember_password_token',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.invite_user',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.log_in_user',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.log_out_user',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.remove_user',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.request_user_remember_password_token',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.sign_up_user_by_invitation',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.application.service.sign_up_user',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user_bundle.security.form_login_user_authenticator',
            Argument::type(Definition::class)
        )->shouldBeCalled();

        $this->process($container);
    }
}
