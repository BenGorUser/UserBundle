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
 * JWT routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class JwtRoutesLoader extends RoutesLoader
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
        $this->routes->add($config['jwt']['name'], new Route(
            $config['jwt']['path'],
            [
                '_controller' => 'BenGorUserBundle:Api\Jwt:newToken',
                'userClass'   => $user,
            ],
            [],
            [],
            '',
            [],
            ['POST']
        ));
    }
}
