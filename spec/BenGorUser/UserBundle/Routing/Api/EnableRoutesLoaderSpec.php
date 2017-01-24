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

use BenGorUser\UserBundle\Routing\Api\EnableRoutesLoader;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Spec file of EnableRoutesLoader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class EnableRoutesLoaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'user' => [
                'enabled'                   => true,
                'type'                      => 'default',
                'api_enabled'               => false,
                'api_type'                  => 'default',
                'name'                      => 'bengor_user_user_enable',
                'path'                      => '/user/confirmation-token',
                'api_name'                  => 'bengor_user_user_api_enable',
                'api_path'                  => '/api/user/confirmation-token',
                'success_redirection_route' => null,
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EnableRoutesLoader::class);
    }

    function it_implements_loader_interface()
    {
        $this->shouldHaveType(LoaderInterface::class);
    }

    function it_loads()
    {
        $this->load('resource');
    }

    function it_supports_bengor_user_enable()
    {
        $this->supports('resource', 'bengor_user_enable_api')->shouldReturn(true);
    }
}
