<?php

/*
 * This file is part of the User bundle.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGor\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegisterServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        $container->setDefinition(
            'bengor.user.infrastructure.persistence.in_memory.user_repository',
            new Definition(
                'BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserRepository'
            )
        );

        foreach ($config['domain']['model']['user_class'] as $key => $class) {
            $container->setDefinition(
                $key . '_password_encoder',
                (new Definition(
                    'Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder',
                    [
                        $class,
                    ]
                ))->setFactory([new Reference('security.encoder_factory'), 'getEncoder'])
            );
            $container->setDefinition(
                'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder',
                new Definition(
                    'BenGor\User\Infrastructure\Security\Symfony\SymfonyUserPasswordEncoder',
                    [
                        $container->getDefinition($key . '_password_encoder'),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory',
                new Definition(
                    'BenGor\User\Infrastructure\Domain\Model\UserFactory',
                    [
                        $class,
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository',
                new Definition(
                    'BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserRepository',
                    [
                        $container->getDefinition('doctrine.orm.default_entity_manager'), $class,
                    ]
                )
            );

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////// APPLICATION SERVICES /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $container->setDefinition(
                'bengor.user.application.service.activate_' . $key . '_account',
                new Definition(
                    'BenGor\User\Application\Service\ActivateUserAccountService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.change_' . $key . '_password',
                new Definition(
                    'BenGor\User\Application\Service\ChangeUserPasswordService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.user_password_encoder'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.change_' . $key . '_password_using_remember_password_token',
                new Definition(
                    'BenGor\User\Application\Service\ChangeUserPasswordUsingRememberPasswordTokenService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.user_password_encoder'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.log_in_' . $key,
                new Definition(
                    'BenGor\User\Application\Service\LogInUserService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.user_password_encoder'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.log_out_' . $key,
                new Definition(
                    'BenGor\User\Application\Service\LogOutUserService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.remove_' . $key,
                new Definition(
                    'BenGor\User\Application\Service\RemoveUserService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.user_password_encoder'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.request_' . $key . '_remember_password_token',
                new Definition(
                    'BenGor\User\Application\Service\RequestRememberPasswordTokenService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.sign_up_' . $key,
                new Definition(
                    'BenGor\User\Application\Service\SignUpUserService',
                    [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.user_password_encoder'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.domain.model.' . $key . '_factory'
                        ),
                    ]
                )
            );
        }
    }
}
