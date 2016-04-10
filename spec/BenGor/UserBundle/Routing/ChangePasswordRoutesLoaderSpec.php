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

use BenGor\UserBundle\Routing\ChangePasswordRoutesLoader;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Spec file of ChangePasswordRoutesLoader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordRoutesLoaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'user' => [
                'enabled'                   => true,
                'type'                      => 'default',
                'name'                      => 'bengor_user_user_change_password',
                'path'                      => '/user/change-password',
                'success_redirection_route' => null,
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChangePasswordRoutesLoader::class);
    }

    function it_implements_loader_interface()
    {
        $this->shouldHaveType(LoaderInterface::class);
    }

    function it_loads()
    {
        $this->load('resource');
    }

    function it_supports_bengor_user_change_password()
    {
        $this->supports('resource', 'bengor_user_change_password')->shouldReturn(true);
    }
}
