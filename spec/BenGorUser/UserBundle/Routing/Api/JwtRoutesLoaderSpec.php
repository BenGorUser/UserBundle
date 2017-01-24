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

use BenGorUser\UserBundle\Routing\Api\JwtRoutesLoader;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Spec file of JWT routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class JwtRoutesLoaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'user' => [
                'enabled'   => false,
                'new_token' => [
                    'name' => 'bengor_user_user_jwt_new_token',
                    'path' => '/user/api/token',
                ],
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JwtRoutesLoader::class);
    }

    function it_implements_loader_interface()
    {
        $this->shouldHaveType(LoaderInterface::class);
    }

    function it_loads()
    {
        $this->load('resource');
    }

    function it_supports_bengor_user_jwt()
    {
        $this->supports('resource', 'bengor_user_jwt')->shouldReturn(true);
    }
}
