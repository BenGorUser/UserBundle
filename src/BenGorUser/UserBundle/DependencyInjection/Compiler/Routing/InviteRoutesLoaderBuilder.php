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
 * Invite routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class InviteRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.invite_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRouteName($user)
    {
        return sprintf('bengor_user_%s_invite', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRoutePath($user)
    {
        return sprintf('/%s/invite', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionApiName()
    {
        return 'bengor.user_bundle.routing.api_invite_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRouteName($user)
    {
        return sprintf('bengor_user_%s_api_invite', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRoutePath($user)
    {
        return sprintf('/api/%s/invite', $user);
    }
}
