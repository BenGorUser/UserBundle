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

use BenGor\UserBundle\Routing\SignUpRoutesLoader;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Spec file of sign up routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegistrationRoutesLoaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'user' => [
                'class'       => 'AppBundle\Entity\User',
                'persistence' => 'doctrine',
                'firewall'    => 'main',
                'use_cases'   => [
                    'registration' => [
                        'enabled' => true,
                        'type'    => 'by_invitation',
                    ],
                ],
                'routes'      => [
                    'registration' => [
                        'name'                      => 'bengor_user_user_registration',
                        'path'                      => '/user/register',
                        'invitation_name'           => 'bengor_user_user_invitation',
                        'invitation_path'           => '/user/invite',
                        'success_redirection_route' => 'bengor_user_user_homepage',
                    ],
                ],
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
}
