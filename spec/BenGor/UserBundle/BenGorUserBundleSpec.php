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

namespace spec\BenGor\UserBundle;

use BenGor\UserBundle\BenGorUserBundle;
use BenGor\UserBundle\DependencyInjection\Compiler\AliasDoctrineServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\LoadDoctrineCustomTypesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\LoadRoutesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\RegisterServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\RegisterTransactionalServicesCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Spec file of bengor user bundle class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class BenGorUserBundleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BenGorUserBundle::class);
    }

    function it_extends_symfony_bundle()
    {
        $this->shouldHaveType(Bundle::class);
    }

    function it_builds(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            Argument::type(RegisterServicesCompilerPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(RegisterTransactionalServicesCompilerPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(AliasDoctrineServicesCompilerPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(LoadDoctrineCustomTypesCompilerPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(LoadRoutesCompilerPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $this->build($container);
    }
}
