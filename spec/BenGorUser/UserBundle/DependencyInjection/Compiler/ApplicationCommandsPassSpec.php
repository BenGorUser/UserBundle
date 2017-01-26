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

namespace spec\BenGorUser\UserBundle\DependencyInjection\Compiler;

use BenGorUser\User\Domain\Model\User;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationCommandsPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of ApplicationCommandsPass class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationCommandsPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationCommandsPass::class);
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class'         => 'AppBundle\Entity\User',
                    'firewall'      => 'main',
                    'persistence'   => 'doctrine_orm',
                    'default_roles' => [
                        'ROLE_USER',
                    ],
                    'use_cases'     => [
                        'security'        => [
                            'enabled' => true,
                        ],
                        'sign_up'         => [
                            'enabled'  => true,
                            'type'     => 'default',
                            'api_type' => 'default',
                        ],
                        'change_password' => [
                            'enabled'  => true,
                            'type'     => 'default',
                            'api_type' => 'default',
                        ],
                        'remove'          => [
                            'enabled' => true,
                        ],
                    ],
                    'routes'        => [
                        'security'                  => [
                            'login'                     => [
                                'name' => 'bengor_user_user_login',
                                'path' => '/user/login',
                            ],
                            'login_check'               => [
                                'name' => 'bengor_user_user_login_check',
                                'path' => '/user/login_check',
                            ],
                            'logout'                    => [
                                'name' => 'bengor_user_user_logout',
                                'path' => '/user/logout',
                            ],
                            'success_redirection_route' => [
                                'route' => 'bengor_user_user_homepage',
                            ],
                            'jwt'                       => [
                                'name' => 'bengor_user_user_jwt_new_token',
                                'path' => '/user/api/token',
                            ],
                        ],
                        'sign_up'                   => [
                            'name'                      => 'bengor_user_user_sign_up',
                            'path'                      => '/user/sign-up',
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                        'invite'                    => [
                            'name'                      => 'bengor_user_user_invite',
                            'path'                      => '/user/invite',
                            'success_redirection_route' => null,
                        ],
                        'enable'                    => [
                            'name'                      => 'bengor_user_user_enable',
                            'path'                      => '/user/confirmation-token',
                            'success_redirection_route' => null,
                        ],
                        'change_password'           => [
                            'name'                      => 'bengor_user_user_change_password',
                            'path'                      => '/user/change-password',
                            'success_redirection_route' => null,
                        ],
                        'request_remember_password' => [
                            'name'                      => 'bengor_user_user_request_remember_password',
                            'path'                      => '/user/remember-password',
                            'success_redirection_route' => null,
                        ],
                        'remove'                    => [
                            'name'                      => 'bengor_user_user_remove',
                            'path'                      => '/user/remove',
                            'success_redirection_route' => null,
                        ],
                    ],
                ],
            ],
        ]);

        $container->getDefinition('bengor.user.infrastructure.persistence.user_repository')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user.infrastructure.security.symfony.user_password_encoder')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user.infrastructure.domain.model.user_factory_sign_up')
            ->shouldBeCalled()->willReturn($definition);

        $container->setDefinition(
            'bengor.user.application.command.log_in_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.log_in',
            'bengor.user.application.command.log_in_user'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.command.log_out_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.log_out',
            'bengor.user.application.command.log_out_user'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.command.sign_up_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.sign_up',
            'bengor.user.application.command.sign_up_user'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.command.change_user_password',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.change_password',
            'bengor.user.application.command.change_user_password'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.command.change_user_password_without_old_password',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.change_password_without_old_password',
            'bengor.user.application.command.change_user_password_without_old_password'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.command.remove_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.remove',
            'bengor.user.application.command.remove_user'
        )->shouldBeCalled();

        $this->process($container);
    }
}
