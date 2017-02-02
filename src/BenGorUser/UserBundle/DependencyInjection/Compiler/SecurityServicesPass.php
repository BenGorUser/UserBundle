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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler;

use BenGorUser\UserBundle\DependencyInjection\Compiler\Routing\SecurityRoutesLoaderBuilder;
use BenGorUser\UserBundle\Security\FormLoginAuthenticator;
use BenGorUser\UserBundle\Security\UserProvider;
use BenGorUser\UserBundle\Security\UserSymfonyDataTransformer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Security services compiler pass.
 *
 * @author Gorka Laucirica <gorka.lauzirila@gmail.com>
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $this->formLoginAuthenticator(
                $container,
                (new SecurityRoutesLoaderBuilder(
                    $container,
                    [$key => $config['user_class'][$key]['routes']['security']]
                ))->configuration(),
                $key
            );
            $this->userSymfonyDataTransformer($container, $key);
            $this->userProvider($container, $key);
        }
    }

    /**
     * Registers service of the form login authenticator and its alias.
     *
     * @param ContainerBuilder $container The container builder
     * @param array            $routes    Array which contains security routes
     * @param string           $user      The user name
     */
    private function formLoginAuthenticator(ContainerBuilder $container, $routes, $user)
    {
        $container->setDefinition(
            'bengor.user_bundle.security.authenticator.form_login_' . $user . '_authenticator',
            new Definition(
                FormLoginAuthenticator::class, [
                    $container->getDefinition('router.default'),
                    $container->getDefinition('bengor_user.' . $user . '.command_bus'),
                    [
                        'login'                     => $routes[$user]['login']['name'],
                        'login_check'               => $routes[$user]['login_check']['name'],
                        'success_redirection_route' => $routes[$user]['success_redirection_route'],
                    ],
                ]
            )
        )->setPublic(false);

        $container->setAlias(
            'bengor_user.' . $user . '.form_login_authenticator',
            'bengor.user_bundle.security.authenticator.form_login_' . $user . '_authenticator'
        );
    }

    /**
     * Registers service of the user Symfony data transformer and its alias.
     *
     * @param ContainerBuilder $container The container builder
     * @param string           $user      The user name
     */
    private function userSymfonyDataTransformer(ContainerBuilder $container, $user)
    {
        $container->setDefinition(
            'bengor.user_bundle.security.' . $user . '_symfony_data_transformer',
            new Definition(
                UserSymfonyDataTransformer::class
            )
        )->setPublic(false);

        $container->setAlias(
            'bengor_user.' . $user . '.symfony_data_transformer',
            'bengor.user_bundle.security.' . $user . '_symfony_data_transformer'
        );
    }

    /**
     * Registers service of the user provider and its alias.
     *
     * @param ContainerBuilder $container The container builder
     * @param string           $user      The user name
     */
    private function userProvider(ContainerBuilder $container, $user)
    {
        $container->setDefinition(
            'bengor.user_bundle.security.' . $user . '_provider',
            new Definition(
                UserProvider::class, [
                    $container->getDefinition(
                        'bengor.user.application.query.' . $user . '_of_email'
                    ),
                    $container->getDefinition(
                        'bengor.user_bundle.security.' . $user . '_symfony_data_transformer'
                    ),
                ]
            )
        )->setPublic(false);

        $container->setAlias(
            'bengor_user.' . $user . '.provider',
            'bengor.user_bundle.security.' . $user . '_provider'
        );
    }
}
