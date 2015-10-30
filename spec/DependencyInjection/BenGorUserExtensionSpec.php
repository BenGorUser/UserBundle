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

namespace spec\BenGor\UserBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Spec file of bengor user extension class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class BenGorUserExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('BenGor\UserBundle\DependencyInjection\BenGorUserExtension');
    }

    function it_extends_symfony_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }

    function it_loads(ContainerBuilder $container)
    {
        $container->setParameter('bengor_user.config', [
            'domain' => ['model' => ['user_class' => ['user' => 'BenGor\User\Domain\Model\User']]],
        ])->shouldBeCalled();

        $this->load([], $container);
    }
}
