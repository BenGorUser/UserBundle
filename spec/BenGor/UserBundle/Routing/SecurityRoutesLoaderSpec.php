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

namespace spec\BenGor\UserBundle\Routing;

use BenGor\UserBundle\Routing\SecurityRoutesLoader;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Spec file of security routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityRoutesLoaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'user' => [
                'class'       => 'AppBundle\Entity\User',
                'persistence' => 'doctrine',
                'firewall'    => 'main',
                'use_cases'   => [
                    'security' => [
                        'enabled' => true,
                    ],
                ],
                'routes'      => [
                    'security' => [
                        'login'                     => [
                            'name' => 'bengor_user_user_security_login',
                            'path' => '/login',
                        ],
                        'login_check'               => [
                            'name' => 'bengor_user_user_security_login_check',
                            'path' => '/login_check',
                        ],
                        'logout'                    => [
                            'name' => 'bengor_user_user_security_logout',
                            'path' => '/logout',
                        ],
                        'success_redirection_route' => 'bengor_user_user_homepage',
                    ],
                ],
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SecurityRoutesLoader::class);
    }

    function it_implements_loader_interface()
    {
        $this->shouldHaveType(LoaderInterface::class);
    }

    function it_loads()
    {
        $this->load('resource');
    }
}
