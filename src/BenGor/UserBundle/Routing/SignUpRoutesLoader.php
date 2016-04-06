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

namespace BenGor\UserBundle\Routing;

use Symfony\Component\Routing\Route;

/**
 * Sing up user routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpRoutesLoader extends RoutesLoader
{
    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user_sign_up' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        $this->routes->add($config['name'], new Route(
            $config['path'],
            [
                '_controller'  => 'BenGorUserBundle:SignUp:' . $config['type'],
                'userClass'    => $user,
                'firewall'     => $config['firewall'],
                'successRoute' => $config['success_redirection_route'],
            ],
            [],
            [],
            '',
            [],
            ['GET', 'POST']
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize($specificationName)
    {
        if ('by_invitation' === $specificationName
            || 'byInvitation' === $specificationName
            || 'by_invitation_with_confirmation' === $specificationName
            || 'byInvitationWithConfirmation' === $specificationName
        ) {
            return 'byInvitation';
        }
        if ('default' === $specificationName
            || 'with_confirmation' === $specificationName
            || 'withConfirmation' === $specificationName
        ) {
            return 'default';
        }
        throw new \RuntimeException('Given sign up type is not support');
    }
}
