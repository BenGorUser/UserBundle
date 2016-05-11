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

namespace BenGorUser\UserBundle\Routing;

use Symfony\Component\Routing\Route;

/**
 * Request remember user password routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RequestRememberPasswordRoutesLoader extends RoutesLoader
{
    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user_request_remember_password' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        if ('default' === $config['type']) {
            return;
        }

        $this->routes->add(
            $config['name'],
            new Route(
                $config['path'],
                [
                    '_controller'  => 'BenGorUserBundle:RequestRememberPassword:requestRememberPassword',
                    'userClass'    => $user,
                    'successRoute' => $config['success_redirection_route'],
                ],
                [],
                [],
                '',
                [],
                ['GET', 'POST']
            )
        );
    }
}
