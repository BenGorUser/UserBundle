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
use BenGor\UserBundle\DependencyInjection\Compiler\ApplicationServicesPass;
use Ddd\Application\Service\TransactionalApplicationService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Spec file of application services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationServicesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationServicesPass::class);
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
        $container->getDefinition('bengor.user.infrastructure.security.symfony.user_password_encoder')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user.application.data_transformer.user_dto')
            ->shouldBeCalled()->willReturn($definition);
        $container->getDefinition('bengor.user.application.data_transformer.user_no_transformation')
            ->shouldBeCalled()->willReturn($definition);

        $container->setDefinition(
            'bengor.user.application.service.log_in_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->getDefinition(
            'bengor.user.infrastructure.routing.symfony_url_generator'
        )->shouldBeCalled()->willReturn($definition);
        $container->getDefinition(
            'bengor.user.application.service.log_in_user'
        )->shouldBeCalled()->willReturn($definition);
        $container->getDefinition(
            'bengor.user.infrastructure.domain.model.user_factory'
        )->shouldBeCalled()->willReturn($definition);
        $container->setDefinition(
            'bengor.user_bundle.security.form_login_user_authenticator',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.form_login_user_authenticator',
            'bengor.user_bundle.security.form_login_user_authenticator'
        )->shouldBeCalled();
        $container->register(
            'bengor.user.application.service.log_in_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(
            false
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.log_in_user',
            'bengor.user.application.service.log_in_user_transactional'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.service.log_out_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->register(
            'bengor.user.application.service.log_out_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(
            false
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.log_out_user',
            'bengor.user.application.service.log_out_user_transactional'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.service.sign_up_user_default',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->register(
            'bengor.user.application.service.sign_up_user_default_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(
            false
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.sign_up_user_default',
            'bengor.user.application.service.sign_up_user_default_transactional'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.service.change_user_password_by_email',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->register(
            'bengor.user.application.service.change_user_password_by_email_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(
            false
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.change_user_password_by_email',
            'bengor.user.application.service.change_user_password_by_email_transactional'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.service.sign_up_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->register(
            'bengor.user.application.service.sign_up_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(
            false
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.sign_up_user',
            'bengor.user.application.service.sign_up_user_transactional'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.service.change_user_password',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->register(
            'bengor.user.application.service.change_user_password_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(
            false
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.change_user_password',
            'bengor.user.application.service.change_user_password_transactional'
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.application.service.remove_user',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->register(
            'bengor.user.application.service.remove_user_transactional',
            TransactionalApplicationService::class
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->addArgument(
            Argument::type(Reference::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(
            false
        )->shouldBeCalled()->willReturn($definition);
        $container->setAlias(
            'bengor_user.remove_user',
            'bengor.user.application.service.remove_user_transactional'
        )->shouldBeCalled();

        $this->process($container);
    }
}
