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
 * JWT routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class JWTRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.jwt_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize(array $configuration)
    {
        foreach ($configuration as $key => $config) {
            if (null === $config['new_token']['name']) {
                $configuration[$key]['new_token']['name'] = $this->defaultNewTokenRouteName($key);
            }
            if (null === $config['new_token']['path']) {
                $configuration[$key]['new_token']['path'] = $this->defaultNewTokenRoutePath($key);
            }
        }

        return $configuration;
    }

    /**
     * Gets the route loader's default new token route name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultNewTokenRouteName($user)
    {
        return sprintf('bengor_user_%s_new_token', $user);
    }

    /**
     * Gets the route loader's default new token route path.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultNewTokenRoutePath($user)
    {
        return sprintf('/%s/api/token', $user);
    }
}
