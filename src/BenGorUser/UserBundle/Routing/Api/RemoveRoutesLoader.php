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

use BenGorUser\UserBundle\Routing\RemoveRoutesLoader as BaseRemoveRoutesLoader;
use Symfony\Component\Routing\Route;

/**
 * Remove user routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RemoveRoutesLoader extends BaseRemoveRoutesLoader
{
    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        $this->routes->add(
            $config['api_name'],
            new Route(
                $config['api_path'],
                [
                    '_controller' => 'BenGorUserBundle:Api\Remove:remove',
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
