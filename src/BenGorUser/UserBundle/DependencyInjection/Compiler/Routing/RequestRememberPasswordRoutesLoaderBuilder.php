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
 * Request remember password routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RequestRememberPasswordRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.request_remember_password_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRouteName($user)
    {
        return sprintf('bengor_user_%s_request_remember_password', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRoutePath($user)
    {
        return sprintf('/%s/remember-password', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionApiName()
    {
        return 'bengor.user_bundle.routing.api_request_remember_password_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRouteName($user)
    {
        return sprintf('bengor_user_%s_api_remember_password', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRoutePath($user)
    {
        return sprintf('/api/%s/remember-password', $user);
    }
}
