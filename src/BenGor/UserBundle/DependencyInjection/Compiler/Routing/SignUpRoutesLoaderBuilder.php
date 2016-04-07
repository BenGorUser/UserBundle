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

namespace BenGor\UserBundle\DependencyInjection\Compiler\Routing;

/**
 * Sign up routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.sign_up_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize(array $configuration)
    {
        $configuration = parent::sanitize($configuration);

        foreach ($configuration as $key => $config) {
            if (null === $config['success_redirection_route']) {
                $configuration[$key]['success_redirection_route'] = $this->defaultSuccessRedirectionRoute($key);
            }
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRouteName($user)
    {
        return sprintf('bengor_user_%s_sign_up', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRoutePath($user)
    {
        return sprintf('/%s/sign-up', $user);
    }

    /**
     * Gets the route loader's default success redirection route.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultSuccessRedirectionRoute($user)
    {
        return sprintf('bengor_user_%s_homepage', $user);
    }
}
