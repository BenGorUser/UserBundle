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
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationQueriesPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of ApplicationQueriesPass pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationQueriesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationQueriesPass::class);
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
                            'enabled' => true,
                            'type'    => 'default',
                        ],
                        'change_password' => [
                            'enabled' => true,
                            'type'    => 'default',
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
                            'success_redirection_route' => 'bengor_user_user_homepage',
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
        $container->getDefinition('bengor.user.application.data_transformer.user_dto')
            ->shouldBeCalled()->willReturn($definition);

        $container->setDefinition(
            'bengor.user.application.query.user_of_email',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.by_email_query',
            'bengor.user.application.query.user_of_email'
        )->shouldBeCalled()->willReturn($container);

        $container->setDefinition(
            'bengor.user.application.query.user_of_invitation_token',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.by_invitation_token_query',
            'bengor.user.application.query.user_of_invitation_token'
        )->shouldBeCalled()->willReturn($container);

        $container->setDefinition(
            'bengor.user.application.query.user_of_remember_password_token',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.user.by_remember_password_token_query',
            'bengor.user.application.query.user_of_remember_password_token'
        )->shouldBeCalled()->willReturn($container);

        $this->process($container);
    }
}
