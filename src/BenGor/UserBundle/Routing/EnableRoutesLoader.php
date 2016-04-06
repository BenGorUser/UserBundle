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
 * Enable user routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class EnableRoutesLoader extends RoutesLoader
{
    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user_enable' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        $this->routes->add(
            $config['name'],
            new Route(
                $config['path'],
                [
                    '_controller'  => 'BenGorUserBundle:Enable:enable',
                    'userClass'    => $user,
                    'successRoute' => $config['success_redirection_route'],
                ],
                [],
                [],
                '',
                [],
                ['GET']
            )
        );
    }
}
