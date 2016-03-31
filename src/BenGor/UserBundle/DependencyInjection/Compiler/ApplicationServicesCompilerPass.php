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
use BenGor\User\Application\Service\EnableUserService;
use BenGor\User\Application\Service\InviteUserService;
use BenGor\User\Application\Service\LogInUserService;
use BenGor\User\Application\Service\LogOutUserService;
use BenGor\User\Application\Service\RemoveUserService;
use BenGor\User\Application\Service\RequestRememberPasswordTokenService;
use BenGor\User\Application\Service\SignUpAndEnableUserByInvitationService;
use BenGor\User\Application\Service\SignUpAndEnableUserService;
use BenGor\User\Application\Service\SignUpUserByInvitationService;
use BenGor\User\Application\Service\SignUpUserService;
use BenGor\UserBundle\Security\FormLoginAuthenticator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Register application services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $guestClass = null;
            if (class_exists($user['class'] . 'Guest')) {
                $guestClass = $user['class'] . 'Guest';
            }

            $container->setDefinition(
                'bengor.user.application.service.enable_' . $key,
                new Definition(
                    EnableUserService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.change_' . $key . '_password',
                new Definition(
                    ChangeUserPasswordService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.' . $key . '_repository'
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
                            'bengor.user.infrastructure.persistence.' . $key . '_repository'
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
                                'bengor.user.infrastructure.persistence.' . $key . '_repository'
                            ),
                            $container->getDefinition(
                                'bengor.user.infrastructure.persistence.' . $key . '_guest_repository'
                            ),
                            $container->getDefinition(
                                'bengor.user.infrastructure.domain.model.' . $key . '_guest_factory'
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
                            'bengor.user.infrastructure.persistence.' . $key . '_repository'
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
                            'bengor.user.infrastructure.persistence.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $container->setDefinition(
                'bengor.user.application.service.remove_' . $key,
                new Definition(
                    RemoveUserService::class, [
                        $container->getDefinition(
                            'bengor.user.infrastructure.persistence.' . $key . '_repository'
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
                            'bengor.user.infrastructure.persistence.' . $key . '_repository'
                        ),
                    ]
                )
            );
            $registrationType = $user['use_cases']['registration']['type'];
            if (null === $guestClass && ('by_invitation' === $registrationType || 'full' === $registrationType)) {
                throw new RuntimeException(
                    'User guest class is not defined so, the "by_invitation" or "full" registration types are invalid'
                );
            }
            $method = 'signUp' . ucfirst(str_replace('_', '', ucwords($registrationType, '_')));
            $container->setDefinition(
                'bengor.user.application.service.sign_up_' . $key,
                $this->$method($container, $key)
            );

// This declaration should be in SecurityServicesCompilerPass file but it requires the
// "bengor.user.application.service.log_in_user"

            $container->setDefinition(
                'bengor.user_bundle.security.form_login_' . $key . '_authenticator',
                new Definition(
                    FormLoginAuthenticator::class, [
                        $container->getDefinition('router.default'),
                        $container->getDefinition('bengor.user.application.service.log_in_' . $key),
                        $container->getDefinition('bengor.user.infrastructure.domain.model.' . $key . '_factory'),
                        [
                            'login'                     => $user['routes']['security']['login']['name'],
                            'login_check'               => $user['routes']['security']['login_check']['name'],
                            'success_redirection_route' => $user['routes']['security']['success_redirection_route'],
                        ],
                    ]
                )
            );
        }
    }

    protected function signUpDefault(ContainerBuilder $container, $key)
    {
        return new Definition(
            SignUpAndEnableUserService::class, [
            $container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_repository'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory'
            ),
        ]);
    }

    protected function signUpUserEnable(ContainerBuilder $container, $key)
    {
        return new Definition(
            SignUpUserService::class, [
            $container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_repository'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory'
            ),
        ]);
    }

    protected function signUpByInvitation(ContainerBuilder $container, $key)
    {
        return new Definition(
            SignUpUserByInvitationService::class, [
            $container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_repository'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_guest_repository'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory'
            ),
        ]);
    }

    protected function signUpFull(ContainerBuilder $container, $key)
    {
        return new Definition(
            SignUpAndEnableUserByInvitationService::class, [
            $container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_repository'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_guest_repository'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder'
            ),
            $container->getDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory'
            ),
        ]);
    }
}
