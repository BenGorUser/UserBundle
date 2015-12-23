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

use BenGor\UserBundle\DependencyInjection\Compiler\AliasDoctrineServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\LoadDoctrineCustomTypesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\LoadRoutesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\RegisterServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\RegisterTransactionalServicesCompilerPass;
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
            new RegisterServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new RegisterTransactionalServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new AliasDoctrineServicesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new LoadDoctrineCustomTypesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
        $container->addCompilerPass(
            new LoadRoutesCompilerPass(), PassConfig::TYPE_OPTIMIZE
        );
    }
}
