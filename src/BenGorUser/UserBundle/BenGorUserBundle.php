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
use BenGorUser\UserBundle\DependencyInjection\Compiler\CommandsServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DefaultRolesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\DomainServicesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\RoutesPass;
use BenGorUser\UserBundle\DependencyInjection\Compiler\SimpleBusPass;
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
            ->addCompilerPass(new SimpleBusPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new RoutesPass(), PassConfig::TYPE_OPTIMIZE)
            ->addCompilerPass(new CommandsServicesPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
