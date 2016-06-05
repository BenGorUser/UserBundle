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

/**
 * Loadable bundle interface.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
interface LoadableBundle
{
    /**
     * Method that exposes the load of bundle's compiler passes
     * and they can be loaded inside other bundle.
     *
     * @param ContainerBuilder $container The container builder
     */
    public function load(ContainerBuilder $container);
}
