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

use BenGorUser\UserBundle\Routing\SignUpRoutesLoader as BaseSignUpRoutesLoader;
use Symfony\Component\Routing\Route;

/**
 * Sing up user routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpRoutesLoader extends BaseSignUpRoutesLoader
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
        $this->routes->add($config['api_name'], new Route(
            $config['api_path'],
            [
                '_controller' => 'BenGorUserBundle:Api\SignUp:' . $config['api_type'],
                'userClass'   => $user,
                'formType'    => $this->formType,
            ],
            [],
            [],
            '',
            [],
            ['POST']
        ));
    }
}
