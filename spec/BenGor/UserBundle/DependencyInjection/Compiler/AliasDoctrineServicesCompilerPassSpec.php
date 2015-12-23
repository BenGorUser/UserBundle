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

use BenGor\User\Infrastructure\Persistence\Doctrine\Types\UserRolesType;
use BenGor\UserBundle\DependencyInjection\Compiler\AliasDoctrineServicesCompilerPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of alias doctrine services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class AliasDoctrineServicesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AliasDoctrineServicesCompilerPass::class);
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
                    'class' => 'BenGor\Domain\Model\User', 'firewall' => [
                        'name' => 'user', 'pattern' => '',
                    ],
                ],
            ],
        ]);

        $container->setAlias(
            'bengor_user.in_memory_user_repository',
            'bengor.user.infrastructure.persistence.in_memory.user_repository'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.in_memory_user_guest_repository',
            'bengor.user.infrastructure.persistence.in_memory.user_guest_repository'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.symfony_user_password_encoder',
            'bengor.user.infrastructure.security.symfony.user_password_encoder'
        )->shouldBeCalled();

        $container->setAlias(
            'bengor_user.user_factory',
            'bengor.user.infrastructure.domain.model.user_factory'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.doctrine_user_repository',
            'bengor.user.infrastructure.persistence.doctrine.user_repository'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.activate_user_account',
            'bengor.user.application.service.activate_user_account_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.change_user_password',
            'bengor.user.application.service.change_user_password_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.change_user_password_using_remember_password_token',
            'bengor.user.application.service.change_user_password_using_remember_password_token_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.log_in_user',
            'bengor.user.application.service.log_in_user_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.log_out_user',
            'bengor.user.application.service.log_out_user_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.remove_user',
            'bengor.user.application.service.remove_user_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.request_user_remember_password_token',
            'bengor.user.application.service.request_user_remember_password_token_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.sign_up_user',
            'bengor.user.application.service.sign_up_user_doctrine_transactional'
        )->shouldBeCalled();
        $container->setAlias(
            'bengor_user.form_login_user_authenticator',
            'bengor.user_bundle.security.form_login_user_authenticator'
        )->shouldBeCalled();

        $this->process($container);
    }
}
