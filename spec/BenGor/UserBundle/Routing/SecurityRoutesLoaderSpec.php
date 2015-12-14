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
        $this->beConstructedWith(['']);
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
