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

namespace BenGor\UserBundle;

use BenGor\UserBundle\DependencyInjection\Compiler\ApplicationDataTransformersPass;
use BenGor\UserBundle\DependencyInjection\Compiler\ApplicationServicesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\CommandsServicesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\DefaultRolesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\DoctrineCustomTypesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\DomainServicesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\MailingServicesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\PersistenceServicesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\RoutesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\SecurityServicesPass;
use BenGor\UserBundle\DependencyInjection\Compiler\SubscribersPass;
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
     * Constructor.
     */
    public function __construct()
    {
        DoctrineCustomTypesPass::odmMongoDb();
    }

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
            ->addCompilerPass(new DoctrineCustomTypesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new SecurityServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new RoutesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new ApplicationDataTransformersPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new ApplicationServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new MailingServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new SubscribersPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new CommandsServicesPass(), PassConfig::TYPE_OPTIMIZE);

        $container
            ->loadFromExtension('doctrine', [
                'orm' => [
                    'mappings' => [
                        'BenGorUserBundle' => [
                            'type'      => 'yml',
                            'is_bundle' => true,
                            'prefix'    => 'BenGor\\User\\Domain\\Model',
                        ],
                    ],
                ],
            ])
            ->loadFromExtension('doctrine_mongodb', [
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
            ])
            ->loadFromExtension('twig', [
                'paths' => [
                    '%kernel.root_dir%/../vendor/bengor/user/src/BenGor/User/Infrastructure/Ui/Twig/views' => 'bengor_user',
                ],
            ]);
    }
}
