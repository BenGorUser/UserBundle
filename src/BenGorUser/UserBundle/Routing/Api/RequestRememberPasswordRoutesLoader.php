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

namespace BenGorUser\UserBundle\Routing\Api;

use BenGorUser\UserBundle\Routing\RoutesLoader;
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
        return 'bengor_user_request_remember_password_api' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        if ('default' === $config['api_type']) {
            return;
        }

        $this->routes->add(
            $config['api_name'],
            new Route(
                $config['api_path'],
                [
                    '_controller' => 'BenGorUserBundle:Api\RequestRememberPassword:requestRememberPassword',
                    'userClass'   => $user,
                ],
                [],
                [],
                '',
                [],
                ['POST']
            )
        );
    }
}
