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

use BenGor\UserBundle\DependencyInjection\Compiler\AliasServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\ApplicationServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\CommandsServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\DefaultRolesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\DoctrineCustomTypesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\DomainServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\MailingServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\PersistenceServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\RoutesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\SecurityServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\SubscribersCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\TransactionalApplicationServicesCompilerPass;
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

        $container->addCompilerPass(
            new DefaultRolesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new DomainServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new PersistenceServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new SecurityServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new DoctrineCustomTypesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new MailingServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new RoutesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new ApplicationServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new TransactionalApplicationServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new AliasServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new SubscribersCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new CommandsServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );

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
        ]);

        $container->loadFromExtension('twig', [
            'paths' => [
                '%kernel.root_dir%/../vendor/bengor/user/src/BenGor/User/Infrastructure/Ui/Twig/views' => 'bengor_user',
            ],
        ]);
    }
}
