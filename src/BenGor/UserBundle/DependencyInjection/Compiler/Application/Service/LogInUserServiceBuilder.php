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

use BenGor\UserBundle\Application\Service\LogInUserService;
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
                        'bengor.user.application.data_transformer.user_no_transformation'
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
     *
     * @param string $user The user name
     */
    private function registerFormLoginAuthenticator($user)
    {
        $routes = $this->configuration['routes'][$user];

        $this->container->setDefinition(
            'bengor.user_bundle.security.form_login_' . $user . '_authenticator',
            new Definition(
                FormLoginAuthenticator::class, [
                    $this->container->getDefinition('bengor.user.infrastructure.routing.symfony_url_generator'),
                    $this->container->getDefinition('bengor.user.application.service.log_in_' . $user),
                    [
                        'login'                     => $routes['login']['name'],
                        'login_check'               => $routes['login_check']['name'],
                        'success_redirection_route' => $routes['success_redirection_route'],
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
