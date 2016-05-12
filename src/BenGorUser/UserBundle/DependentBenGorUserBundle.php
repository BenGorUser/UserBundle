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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Trait that allows to check bundle dependencies.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
trait DependentBenGorUserBundle
{
    /**
     * Checks the given bundles are enabled in the container.
     *
     * @param array            $requiredBundles The required bundles
     * @param ContainerBuilder $container       The container builder
     */
    public function checkDependencies(array $requiredBundles, ContainerBuilder $container)
    {
        if (false === ($this instanceof Bundle)) {
            throw new RuntimeException('It is a bundle trait, you shouldn\'t have to use in other instances');
        }

        $enabledBundles = $container->getParameter('kernel.bundles');
        foreach ($requiredBundles as $requiredBundle) {
            if (!isset($enabledBundles[$requiredBundle])) {
                throw new RuntimeException(
                    sprintf(
                        'In order to use "%s" you also need to enable and configure the "%s"',
                        $this->getName(),
                        $requiredBundle
                    )
                );
            }
        }
    }
}
