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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Routing;

/**
 * Change password routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.change_password_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRouteName($user)
    {
        return sprintf('bengor_user_%s_change_password', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRoutePath($user)
    {
        return sprintf('/%s/change-password', $user);
    }
}
