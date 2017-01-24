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
 * Resend invitation routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ResendInvitationRoutesLoaderBuilder extends RoutesLoaderBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function definitionName()
    {
        return 'bengor.user_bundle.routing.resend_invitation_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRouteName($user)
    {
        return sprintf('bengor_user_%s_resend_invitation', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultRoutePath($user)
    {
        return sprintf('/%s/resend-invitation', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionApiName()
    {
        return 'bengor.user_bundle.routing.api_resend_invitation_routes_loader';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRouteName($user)
    {
        return sprintf('bengor_user_%s_api_resend_invitation', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultApiRoutePath($user)
    {
        return sprintf('/api/%s/resend-invitation', $user);
    }
}
