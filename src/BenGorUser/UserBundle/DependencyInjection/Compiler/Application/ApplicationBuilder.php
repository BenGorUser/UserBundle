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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application;

/**
 * Application builder base interface.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
interface ApplicationBuilder
{
    /**
     * Entry point of application builder to inject the application
     * layer command, query or data transformer inside Symfony DIC.
     *
     * @param string $user The user name
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function build($user);
}
