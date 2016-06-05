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

use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationCommandsPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationDataTransformersPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationQueriesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandsServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DefaultRolesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DomainServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\RoutesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\SecurityServicesPass;
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
            ->addCompilerPass(new ApplicationCommandsPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new ApplicationDataTransformersPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new ApplicationQueriesPass(), PassConfig::TYPE_OPTIMIZE);

        $this->buildLoadableBundles($container);

        $container
            ->addCompilerPass(new RoutesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new SecurityServicesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new CommandsServicesPass(), PassConfig::TYPE_OPTIMIZE);
    }

    /**
     * Executes the load method of LoadableBundle instances.
     *
     * @param ContainerBuilder $container The container builder
     */
    protected function buildLoadableBundles(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        foreach ($bundles as $bundle) {
            $reflectionClass = new \ReflectionClass($bundle);
            if ($reflectionClass->implementsInterface(LoadableBundle::class)) {
                (new $bundle())->load($container);
            }
        }
    }
}
