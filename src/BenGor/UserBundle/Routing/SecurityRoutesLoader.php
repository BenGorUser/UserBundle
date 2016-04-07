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
 * Security routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityRoutesLoader extends RoutesLoader
{
    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user_security' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        $this->routes->add($config['login']['name'], new Route(
            $config['login']['path'],
            [
                '_controller'  => 'BenGorUserBundle:Security:login',
                'successRoute' => $config['success_redirection_route'],
            ],
            [],
            [],
            '',
            [],
            ['GET', 'POST']
        ));
        $this->routes->add($config['login_check']['name'], new Route(
            $config['login_check']['path'],
            [
                '_controller' => 'BenGorUserBundle:Security:loginCheck',
            ],
            [],
            [],
            '',
            [],
            ['POST']
        ));
        $this->routes->add($config['logout']['name'], new Route(
            $config['logout']['path'],
            [
                '_controller' => 'BenGorUserBundle:Security:logout',
            ],
            [],
            [],
            '',
            [],
            ['GET']
        ));
    }
}
