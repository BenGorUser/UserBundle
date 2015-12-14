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

namespace spec\BenGor\UserBundle\DependencyInjection;

use BenGor\UserBundle\DependencyInjection\BenGorUserExtension;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Spec file of bengor user extension class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class BenGorUserExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BenGorUserExtension::class);
    }

    function it_extends_symfony_extension()
    {
        $this->shouldHaveType(Extension::class);
    }

    function it_does_not_loads_because_required_configuration_is_missing(ContainerBuilder $container)
    {
        $this->load([], $container);
    }
}
