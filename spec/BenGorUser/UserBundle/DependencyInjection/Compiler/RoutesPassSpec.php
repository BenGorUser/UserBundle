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

use BenGorUser\UserBundle\DependencyInjection\Compiler\RoutesPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of load routes compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RoutesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RoutesPass::class);
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
                            'enabled'     => true,
                            'api_enabled' => false,
                        ],
                        'sign_up'         => [
                            'enabled'     => true,
                            'type'        => 'default',
                            'api_enabled' => false,
                            'api_type'    => 'default',
                        ],
                        'change_password' => [
                            'enabled'     => true,
                            'type'        => 'default',
                            'api_enabled' => false,
                            'api_type'    => 'default',
                        ],
                        'remove'          => [
                            'enabled'     => true,
                            'api_enabled' => false,
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
                            'api_name'                  => 'bengor_user_user_api_sign_up',
                            'api_path'                  => '/api/user/sign-up',
                        ],
                        'invite'                    => [
                            'name'                      => 'bengor_user_user_invite',
                            'path'                      => '/user/invite',
                            'success_redirection_route' => null,
                            'api_name'                  => 'bengor_user_user_api_invite',
                            'api_path'                  => '/api/user/invite',
                        ],
                        'resend_invitation'         => [
                            'name'                      => 'bengor_user_user_resend_invitation',
                            'path'                      => '/user/resend-invitation',
                            'success_redirection_route' => null,
                            'api_name'                  => 'bengor_user_user_api_resend_invitation',
                            'api_path'                  => '/api/user/resend-invitation',
                        ],
                        'enable'                    => [
                            'name'                      => 'bengor_user_user_enable',
                            'path'                      => '/user/confirmation-token',
                            'success_redirection_route' => null,
                            'api_name'                  => 'bengor_user_user_api_enable',
                            'api_path'                  => '/api/user/enable',
                        ],
                        'change_password'           => [
                            'name'                      => 'bengor_user_user_change_password',
                            'path'                      => '/user/change-password',
                            'success_redirection_route' => null,
                            'api_name'                  => 'bengor_user_user_api_change_password',
                            'api_path'                  => '/api/user/change-password',
                        ],
                        'request_remember_password' => [
                            'name'                      => 'bengor_user_user_request_remember_password',
                            'path'                      => '/user/remember-password',
                            'success_redirection_route' => null,
                            'api_name'                  => 'bengor_user_user_api_request_remember_password',
                            'api_path'                  => '/api/user/request-remember-password',
                        ],
                        'remove'                    => [
                            'name'                      => 'bengor_user_user_remove',
                            'path'                      => '/user/remove',
                            'success_redirection_route' => null,
                            'api_name'                  => 'bengor_user_user_api_remove',
                            'api_path'                  => '/api/user/remove',
                        ],
                    ],
                ],
            ],
        ]);

        $container->hasDefinition('bengor.user_bundle.routing.change_password_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_change_password_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.change_password_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_change_password_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.enable_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_enable_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.enable_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_enable_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.invite_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_invite_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.invite_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_invite_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.security_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.security_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.api_jwt_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.api_jwt_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.sign_up_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_sign_up_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.sign_up_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_sign_up_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.remove_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_remove_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.remove_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_remove_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.request_remember_password_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_request_remember_password_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.request_remember_password_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_request_remember_password_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->hasDefinition('bengor.user_bundle.routing.resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->hasDefinition('bengor.user_bundle.routing.api_resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getDefinition('bengor.user_bundle.routing.resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user_bundle.routing.api_resend_invitation_routes_loader')
            ->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
