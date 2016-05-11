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

namespace spec\BenGorUser\UserBundle\Routing;

use BenGorUser\UserBundle\Routing\SecurityRoutesLoader;
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
                'enabled'                   => true,
                'login'                     => [
                    'name' => 'bengor_user_user_login',
                    'path' => '/user/login',
                ],
                'login_check'               => [
                    'name' => 'bengor_user_user_login_check',
                    'path' => '/user/login_check',
                ],
                'logout'                    => [
                    'name' => 'bengor_user_user_logout',
                    'path' => '/user/logout',
                ],
                'success_redirection_route' => 'bengor_user_user_homepage',
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

    function it_supports_bengor_user_security()
    {
        $this->supports('resource', 'bengor_user_security')->shouldReturn(true);
    }
}
