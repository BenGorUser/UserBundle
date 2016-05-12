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
 * Security routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.security_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize(array $configuration)
    {
        foreach ($configuration as $key => $config) {
            if (null === $config['success_redirection_route']) {
                $configuration[$key]['success_redirection_route'] = $this->defaultSuccessRedirectionRoute($key);
            }
            if (null === $config['login']['name']) {
                $configuration[$key]['login']['name'] = $this->defaultLoginRouteName($key);
            }
            if (null === $config['login']['path']) {
                $configuration[$key]['login']['path'] = $this->defaultLoginRoutePath($key);
            }
            if (null === $config['login_check']['name']) {
                $configuration[$key]['login_check']['name'] = $this->defaultLoginCheckRouteName($key);
            }
            if (null === $config['login_check']['path']) {
                $configuration[$key]['login_check']['path'] = $this->defaultLoginCheckRoutePath($key);
            }
            if (null === $config['logout']['name']) {
                $configuration[$key]['logout']['name'] = $this->defaultLogoutRouteName($key);
            }
            if (null === $config['logout']['path']) {
                $configuration[$key]['logout']['path'] = $this->defaultLogoutRoutePath($key);
            }
        }

        return $configuration;
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

    /**
     * Gets the route loader's default login route name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultLoginRouteName($user)
    {
        return sprintf('bengor_user_%s_login', $user);
    }

    /**
     * Gets the route loader's default login route path.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultLoginRoutePath($user)
    {
        return sprintf('/%s/login', $user);
    }

    /**
     * Gets the route loader's default login check route name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultLoginCheckRouteName($user)
    {
        return sprintf('bengor_user_%s_login_check', $user);
    }

    /**
     * Gets the route loader's default login check route path.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultLoginCheckRoutePath($user)
    {
        return sprintf('/%s/login-check', $user);
    }

    /**
     * Gets the route loader's default logout route name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultLogoutRouteName($user)
    {
        return sprintf('bengor_user_%s_logout', $user);
    }

    /**
     * Gets the route loader's default logout route path.
     *
     * @param string $user The user name
     *
     * @return string
     */
    private function defaultLogoutRoutePath($user)
    {
        return sprintf('/%s/logout', $user);
    }
}
