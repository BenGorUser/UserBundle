<?php

/*
 * This file is part of the User bundle.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\BenGor\UserBundle;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Spec file of bengor user bundle class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class BenGorUserBundleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('BenGor\UserBundle\BenGorUserBundle');
    }

    function it_extends_symfony_bundle()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\Bundle\Bundle');
    }

    function it_builds(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            Argument::type('BenGor\UserBundle\DependencyInjection\Compiler\RegisterServicesCompilerPass')
        )->shouldBeCalled()->willReturn($container);

        $this->build($container);
    }
}
