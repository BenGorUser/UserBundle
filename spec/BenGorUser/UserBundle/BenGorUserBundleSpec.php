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
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationDataTransformersPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandsServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DefaultRolesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DoctrineCustomTypesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DomainServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\MailingServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\PersistenceServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\RoutesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\SecurityServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\SubscribersPass;
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
            Argument::type(ApplicationDataTransformersPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(DefaultRolesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(DomainServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(PersistenceServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(SecurityServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(DoctrineCustomTypesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(MailingServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(ApplicationServicesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(RoutesPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->addCompilerPass(
            Argument::type(SubscribersPass::class),
            PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);
        $container->addCompilerPass(
            Argument::type(CommandsServicesPass::class), PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->hasExtension('doctrine')->shouldBeCalled()->willReturn(true);
        $container->loadFromExtension('doctrine', [
            'orm' => [
                'mappings' => [
                    'BenGorUserBundle' => [
                        'type'      => 'yml',
                        'is_bundle' => true,
                        'prefix'    => 'BenGor\\User\\Domain\\Model',
                    ],
                ],
            ],
        ])->shouldBeCalled()->willReturn($container);

        $container->hasExtension('doctrine_mongodb')->shouldBeCalled()->willReturn(true);
        $container->loadFromExtension('doctrine_mongodb', [
            'document_managers' => [
                'default' => [
                    'mappings' => [
                        'BenGorUserBundle' => [
                            'type'      => 'yml',
                            'is_bundle' => true,
                            'prefix'    => 'BenGor\\User\\Domain\\Model',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled()->willReturn($container);

        $container->hasExtension('framework')->shouldBeCalled()->willReturn(true);
        $container->loadFromExtension('framework', [
            'translator' => [
                'paths' => [
                    '%kernel.root_dir%/../vendor/bengor/user/src/BenGor/User/Infrastructure/Ui/Translations',
                ],
            ],
        ])->shouldBeCalled()->willReturn($container);

        $container->hasExtension('twig')->shouldBeCalled()->willReturn(true);
        $container->loadFromExtension('twig', [
            'paths' => [
                '%kernel.root_dir%/../vendor/bengor/user/src/BenGor/User/Infrastructure/Ui/Twig/views' => 'bengor_user',
            ],
        ])->shouldBeCalled()->willReturn($container);

        $this->build($container);
    }
}
