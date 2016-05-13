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

namespace BenGorUser\UserBundle;

use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationDataTransformersPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandBusPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandsServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DefaultRolesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DomainServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\RoutesPass;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\AddMiddlewareTags;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\CompilerPassUtil;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * BenGor user bundle kernel class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class BenGorUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new DefaultRolesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new DomainServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new ApplicationDataTransformersPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new ApplicationServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new CommandBusPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new RoutesPass(), PassConfig::TYPE_OPTIMIZE);


        $container->addCompilerPass(new CommandsServicesPass(), PassConfig::TYPE_OPTIMIZE);

//        CompilerPassUtil::prependBeforeOptimizationPass(
//            $container,
//            new AddMiddlewareTags(
//                'simple_bus.event_bus.handles_recorded_messages_middleware',
//                ['command'],
//                200
//            )
//        );

        $container->loadFromExtension('doctrine', [
            'orm' => [
                'mappings' => [
                    'BenGorUserBundle' => [
                        'type'      => 'yml',
                        'is_bundle' => true,
                        'prefix'    => 'BenGorUser\\User\\Domain\\Model',
                    ],
                ],
            ],
        ]);

    }
}
