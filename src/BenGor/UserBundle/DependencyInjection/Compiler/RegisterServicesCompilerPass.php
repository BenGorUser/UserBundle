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

namespace BenGor\UserBundle\DependencyInjection\Compiler;

use BenGor\User\Application\Service\ActivateUserAccountService;
use BenGor\User\Application\Service\ChangeUserPasswordService;
use BenGor\User\Application\Service\ChangeUserPasswordUsingRememberPasswordTokenService;
use BenGor\User\Application\Service\InviteUserService;
use BenGor\User\Application\Service\LogInUserService;
use BenGor\User\Application\Service\LogOutUserService;
use BenGor\User\Application\Service\RemoveUserService;
use BenGor\User\Application\Service\RequestRememberPasswordTokenService;
use BenGor\User\Application\Service\SignUpUserByInvitationService;
use BenGor\User\Application\Service\SignUpUserService;
use BenGor\User\Infrastructure\Domain\Model\UserFactory;
use BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserRepository;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use BenGor\User\Infrastructure\Security\Symfony\SymfonyUserPasswordEncoder;
use BenGor\UserBundle\Security\FormLoginAuthenticator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

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
            new Definition(InMemoryUserRepository::class)
        );
        $container->setDefinition(
            'bengor.user.infrastructure.persistence.in_memory.user_guest_repository',
            new Definition(InMemoryUserGuestRepository::class)
        );
        foreach ($config['user_class'] as $key => $user) {
            $guestClass = null;
            if (class_exists($user['class'] . 'Guest')) {
                $guestClass = $user['class'] . 'Guest';
            }

            $container->setDefinition(
                $key . '_password_encoder',
                (new Definition(
                    BCryptPasswordEncoder::class, [
                        $user['class'],
                    ]
                ))->setFactory([new Reference('security.encoder_factory'), 'getEncoder'])
            );
            $container->setDefinition(
                'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder',
                new Definition(
                    SymfonyUserPasswordEncoder::class, [
                        $container->getDefinition($key . '_password_encoder'),
                    ]
                )
            );

            $container->setDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory',
                new Definition(
                    UserFactory::class, [
                        $user['class'],
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository',
                (new Definition(
                    DoctrineUserRepository::class, [
                        $user['class'],
                    ]
                ))->setFactory([new Reference('doctrine.orm.default_entity_manager'), 'getRepository'])
            );
            if (null !== $guestClass) {
                $container->setDefinition(
                    'bengor.user.infrastructure.persistence.doctrine.' . $key . '_guest_repository',
                    new Definition(
                        DoctrineUserGuestRepository::class, [
                            $container->getDefinition('doctrine.orm.default_entity_manager'), $guestClass,
                        ]
                    )
                );
            }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////// APPLICATION SERVICES ///////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $container->setDefinition(
                'bengor.user.application.service.activate_' . $key . '_account',
                new Definition(
                    ActivateUserAccountService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.change_' . $key . '_password',
                new Definition(
                    ChangeUserPasswordService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.change_' . $key . '_password_using_remember_password_token',
                new Definition(
                    ChangeUserPasswordUsingRememberPasswordTokenService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
                        ),
                    ]
                )
            );
            if (null !== $guestClass) {
                $container->setDefinition(
                    'bengor.user.application.service.invite_' . $key,
                    new Definition(
                        InviteUserService::class, [
                            $container->getDefinition(
                                'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                            ),
                            $container->getDefinition(
                                'bengor.user.infrastructure.persistence.doctrine.' . $key . '_guest_repository'
                            ),
                        ]
                    )
                );
            }
            $container->setDefinition(
                'bengor.user.application.service.log_in_' . $key,
                new Definition(
                    LogInUserService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.log_out_' . $key,
                new Definition(
                    LogOutUserService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.remove_' . $key,
                new Definition(
                    RemoveUserService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.request_' . $key . '_remember_password_token',
                new Definition(
                    RequestRememberPasswordTokenService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                    ]
                )
            );
            if (null !== $guestClass) {
                $container->setDefinition(
                    'bengor.user.application.service.sign_up_' . $key . '_by_invitation',
                    new Definition(
                        SignUpUserByInvitationService::class, [
                            $container->getDefinition(
                                'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                            ),
                            $container->getDefinition(
                                'bengor.user.infrastructure.persistence.doctrine.' . $key . '_guest_repository'
                            ),
                            $container->getDefinition(
                                'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
                            ),
                            $container->getDefinition(
                                'bengor.user.infrastructure.domain.model.' . $key . '_factory'
                            ),
                        ]
                    )
                );
            }
            $container->setDefinition(
                'bengor.user.application.service.sign_up_' . $key,
                new Definition(
                    SignUpUserService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.domain.model.' . $key . '_factory'
                        ),
                    ]
                )
            );

            $container->setDefinition(
                'bengor.user_bundle.security.form_login_' . $key . '_authenticator',
                new Definition(
                    FormLoginAuthenticator::class, [
                        $container->getDefinition('router.default'),
                        $container->getDefinition('bengor.user.application.service.log_in_' . $key),
                        $container->getDefinition('bengor.user.infrastructure.domain.model.' . $key . '_factory'),
                        $user['firewall']['pattern'],
                    ]
                )
            );
        }
    }
}
