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

namespace BenGorUser\UserBundle\Routing;

use Symfony\Component\Routing\Route;

/**
 * JWT routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class JWTRoutesLoader extends RoutesLoader
{
    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user_jwt' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        $this->routes->add($config['new_token']['name'], new Route(
            $config['new_token']['path'],
            [
                '_controller'  => 'BenGorUserBundle:JWT:newToken',
                'userClass'    => $user,
            ],
            [],
            [],
            '',
            [],
            ['POST']
        ));
    }
}
