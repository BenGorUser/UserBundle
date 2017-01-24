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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Routing;

/**
 * Remove routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RemoveRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function sanitize(array $configuration)
    {
        foreach ($configuration as $key => $config) {
            if (null === $config['name']) {
                $configuration[$key]['name'] = $this->defaultRouteName($key);
            }
            if (null === $config['path']) {
                $configuration[$key]['path'] = $this->defaultRoutePath($key);
            }
            if (null === $config['api_name']) {
                $configuration[$key]['api_name'] = $this->defaultApiRouteName($key);
            }
            if (null === $config['api_path']) {
                $configuration[$key]['api_path'] = $this->defaultApiRoutePath($key);
            }
            if (null === $config['success_redirection_route']) {
                $configuration[$key]['success_redirection_route'] = $this->defaultSuccessRedirectionRoute($key);
            }
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.remove_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRouteName($user)
    {
        return sprintf('bengor_user_%s_remove', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRoutePath($user)
    {
        return sprintf('/%s/remove', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionApiName()
    {
        return 'bengor.user_bundle.routing.api_remove_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRouteName($user)
    {
        return sprintf('bengor_user_%s_api_remove', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRoutePath($user)
    {
        return sprintf('/api/%s/remove', $user);
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
