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

namespace spec\BenGorUser\UserBundle;

use BenGorUser\UserBundle\BenGorUserBundle;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationCommandsPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationDataTransformersPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationQueriesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandsServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DefaultRolesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DomainServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\RoutesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\SecurityServicesPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Spec file of BenGorUserBundle class.
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
            Argument::type(DefaultRolesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(DomainServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(ApplicationCommandsPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(ApplicationDataTransformersPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(ApplicationQueriesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->getParameter('kernel.bundles')->shouldBeCalled()->willReturn([
            'FrameworkBundle' => 'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
        ]);

        $container->addCompilerPass(
            Argument::type(RoutesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(SecurityServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(CommandsServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $this->build($container);
    }
}
