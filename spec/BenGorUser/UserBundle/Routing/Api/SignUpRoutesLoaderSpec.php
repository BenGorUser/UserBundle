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

namespace spec\BenGorUser\UserBundle\Routing\Api;

use BenGorUser\UserBundle\Routing\Api\SignUpRoutesLoader;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Spec file of SignUpRoutesLoader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpRoutesLoaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'user' => [
                'enabled'                   => true,
                'type'                      => 'default',
                'api_enabled'               => false,
                'api_type'                  => 'default',
                'firewall'                  => 'main',
                'name'                      => 'bengor_user_user_sign_up',
                'path'                      => '/user/sign-up',
                'success_redirection_route' => 'bengor_user_user_homepage',
                'api_name'                  => 'bengor_user_user_api_sign_up',
                'api_path'                  => '/api/user/sign-up',
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SignUpRoutesLoader::class);
    }

    function it_implements_loader_interface()
    {
        $this->shouldHaveType(LoaderInterface::class);
    }

    function it_loads()
    {
        $this->load('resource');
    }

    function it_supports_bengor_user_sign_up()
    {
        $this->supports('resource', 'bengor_user_sign_up_api')->shouldReturn(true);
    }
}
