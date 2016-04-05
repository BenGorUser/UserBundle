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

namespace BenGor\UserBundle\DependencyInjection\Compiler\Application\Service;

use BenGor\User\Application\Service\LogIn\LogInUserService;
use BenGor\UserBundle\Security\FormLoginAuthenticator;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Log in user service builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class LogInUserServiceBuilder extends ServiceBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $this->container->setDefinition(
            $this->definitionName($user),
            new Definition(
                LogInUserService::class, [
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.persistence.' . $user . '_repository'
                    ),
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.security.symfony.' . $user . '_password_encoder'
                    ),
                    $this->container->getDefinition(
                        'bengor.user.application.data_transformer.user_dto'
                    ),
                ]
            )
        );

        $this->registerFormLoginAuthenticator($user);
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.service.log_in_' . $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.log_in_' . $user;
    }

    /**
     * Registers the form login authenticator.
     *
     * This declaration should be in SecurityServicesCompilerPass file
     * but it requires the "bengor.user.application.service.log_in_user".
     */
    private function registerFormLoginAuthenticator($user)
    {
        $this->container->setDefinition(
            'bengor.user_bundle.security.form_login_' . $user . '_authenticator',
            new Definition(
                FormLoginAuthenticator::class, [
                    $this->container->getDefinition('router.default'),
                    $this->container->getDefinition('bengor.user.application.service.log_in_' . $user),
                    $this->container->getDefinition('bengor.user.infrastructure.domain.model.' . $user . '_factory'),
                    [
                        'login'                     => $this->configuration['routes']['login']['name'],
                        'login_check'               => $this->configuration['routes']['login_check']['name'],
                        'success_redirection_route' => $this->configuration['routes']['success_redirection_route'],
                    ],
                ]
            )
        )->setPublic(false);

        $this->container->setAlias(
            'bengor_user.form_login_' . $user . '_authenticator',
            'bengor.user_bundle.security.form_login_' . $user . '_authenticator'
        );
    }
}
