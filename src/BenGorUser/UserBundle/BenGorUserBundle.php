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

namespace BenGorUser\UserBundle;

use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationDataTransformersPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandBusPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandsServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DefaultRolesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DomainServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\PersistenceServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\RoutesPass;
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
        parent::build($container);

        $container
            ->addCompilerPass(new DefaultRolesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new DomainServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new PersistenceServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new CommandBusPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new RoutesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new ApplicationDataTransformersPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new CommandsServicesPass(), PassConfig::TYPE_OPTIMIZE);

        if (true === $container->hasExtension('doctrine_mongodb')) {
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
            ]);
        }
//        if (true === $container->hasExtension('doctrine')) {
//            $container->loadFromExtension('doctrine', [
//                'orm' => [
//                    'mappings' => [
//                        'BenGorUserBundle' => [
//                            'type'      => 'yml',
//                            'is_bundle' => true,
//                            'prefix'    => 'BenGor\\User\\Domain\\Model',
//                        ],
//                    ],
//                ],
//            ]);
//        }
//        if (true === $container->hasExtension('framework')) {
//            $container->loadFromExtension('framework', [
//                'translator' => [
//                    'paths' => [
//                        '%kernel.root_dir%/../vendor/bengor/user/src/BenGor/User/Infrastructure/Ui/Translations',
//                    ],
//                ],
//            ]);
//        }
//        if (true === $container->hasExtension('twig')) {
//            $container->loadFromExtension('twig', [
//                'paths' => [
//                    '%kernel.root_dir%/../vendor/bengor/user/src/BenGor/User/Infrastructure/Ui/Twig/views' => 'bengor_user',
//                ],
//            ]);
//        }
    }
}
