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
use BenGor\UserBundle\DependencyInjection\Compiler\RoutesCompilerPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of load routes compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RoutesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RoutesCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('bengor.user_bundle.routing.security_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class'    => User::class,
                    'firewall' => 'main',
                    'routes'   => [
                        'security'     => [
                            'enabled'                   => true,
                            'login'                     => [
                                'name' => 'bengor_user_user_security_login',
                                'path' => '/login',
                            ],
                            'login_check'               => [
                                'name' => 'bengor_user_user_security_login_check',
                                'path' => '/login_check',
                            ],
                            'logout'                    => [
                                'name' => 'bengor_user_user_security_logout',
                                'path' => '/logout',
                            ],
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                        'registration' => [
                            'enabled'                   => true,
                            'type'                      => 'by_invitation',
                            'name'                      => 'bengor_user_user_registration',
                            'path'                      => '/user/register',
                            'invitation_name'           => 'bengor_user_user_invitation',
                            'invitation_path'           => '/user/invite',
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                    ],
                ],
            ],
        ]);
        $container->setParameter('bengor_user.config', [
            'user_class' => [
                'user' => [
                    'class'    => 'BenGor\User\Domain\Model\User',
                    'firewall' => 'main',
                    'routes'   => [
                        'security'     => [
                            'enabled'                   => true,
                            'login'                     => [
                                'name' => 'bengor_user_user_security_login',
                                'path' => '/login',
                            ],
                            'login_check'               => [
                                'name' => 'bengor_user_user_security_login_check',
                                'path' => '/login_check',
                            ],
                            'logout'                    => [
                                'name' => 'bengor_user_user_security_logout',
                                'path' => '/logout',
                            ],
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                        'registration' => [
                            'enabled'                   => true,
                            'type'                      => 'by_invitation',
                            'name'                      => 'bengor_user_user_registration',
                            'path'                      => '/user/register',
                            'invitation_name'           => 'bengor_user_user_invitation',
                            'invitation_path'           => '/user/invite',
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled()->willReturn($container);
        $container->getDefinition('bengor.user_bundle.routing.security_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $definition->replaceArgument(0, [
            'user' => [
                'class'    => 'BenGor\User\Domain\Model\User',
                'firewall' => 'main',
                'routes'   => [
                    'security'     => [
                        'enabled'                   => true,
                        'login'                     => [
                            'name' => 'bengor_user_user_security_login',
                            'path' => '/login',
                        ],
                        'login_check'               => [
                            'name' => 'bengor_user_user_security_login_check',
                            'path' => '/login_check',
                        ],
                        'logout'                    => [
                            'name' => 'bengor_user_user_security_logout',
                            'path' => '/logout',
                        ],
                        'success_redirection_route' => 'bengor_user_user_homepage',
                    ],
                    'registration' => [
                        'enabled'                   => true,
                        'type'                      => 'by_invitation',
                        'name'                      => 'bengor_user_user_registration',
                        'path'                      => '/user/register',
                        'invitation_name'           => 'bengor_user_user_invitation',
                        'invitation_path'           => '/user/invite',
                        'success_redirection_route' => 'bengor_user_user_homepage',
                    ],
                ],
            ],
        ])->shouldBeCalled()->willReturn($definition);

        $container->hasDefinition('bengor.user_bundle.routing.registration_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class'    => User::class,
                    'firewall' => 'main',
                    'routes'   => [
                        'security'     => [
                            'enabled'                   => true,
                            'login'                     => [
                                'name' => 'bengor_user_user_security_login',
                                'path' => '/login',
                            ],
                            'login_check'               => [
                                'name' => 'bengor_user_user_security_login_check',
                                'path' => '/login_check',
                            ],
                            'logout'                    => [
                                'name' => 'bengor_user_user_security_logout',
                                'path' => '/logout',
                            ],
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                        'registration' => [
                            'enabled'                   => true,
                            'type'                      => 'by_invitation',
                            'name'                      => 'bengor_user_user_registration',
                            'path'                      => '/user/register',
                            'invitation_name'           => 'bengor_user_user_invitation',
                            'invitation_path'           => '/user/invite',
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                    ],
                ],
            ],
        ]);
        $container->setParameter('bengor_user.config', [
            'user_class' => [
                'user' => [
                    'class'    => 'BenGor\User\Domain\Model\User',
                    'firewall' => 'main',
                    'routes'   => [
                        'security'     => [
                            'enabled'                   => true,
                            'login'                     => [
                                'name' => 'bengor_user_user_security_login',
                                'path' => '/login',
                            ],
                            'login_check'               => [
                                'name' => 'bengor_user_user_security_login_check',
                                'path' => '/login_check',
                            ],
                            'logout'                    => [
                                'name' => 'bengor_user_user_security_logout',
                                'path' => '/logout',
                            ],
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                        'registration' => [
                            'enabled'                   => true,
                            'type'                      => 'by_invitation',
                            'name'                      => 'bengor_user_user_registration',
                            'path'                      => '/user/register',
                            'invitation_name'           => 'bengor_user_user_invitation',
                            'invitation_path'           => '/user/invite',
                            'success_redirection_route' => 'bengor_user_user_homepage',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled()->willReturn($container);
        $container->getDefinition('bengor.user_bundle.routing.registration_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $definition->replaceArgument(0, [
            'user' => [
                'class'    => 'BenGor\User\Domain\Model\User',
                'firewall' => 'main',
                'routes'   => [
                    'security'     => [
                        'enabled'                   => true,
                        'login'                     => [
                            'name' => 'bengor_user_user_security_login',
                            'path' => '/login',
                        ],
                        'login_check'               => [
                            'name' => 'bengor_user_user_security_login_check',
                            'path' => '/login_check',
                        ],
                        'logout'                    => [
                            'name' => 'bengor_user_user_security_logout',
                            'path' => '/logout',
                        ],
                        'success_redirection_route' => 'bengor_user_user_homepage',
                    ],
                    'registration' => [
                        'enabled'                   => true,
                        'type'                      => 'by_invitation',
                        'name'                      => 'bengor_user_user_registration',
                        'path'                      => '/user/register',
                        'invitation_name'           => 'bengor_user_user_invitation',
                        'invitation_path'           => '/user/invite',
                        'success_redirection_route' => 'bengor_user_user_homepage',
                    ],
                ],
            ],
        ])->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
