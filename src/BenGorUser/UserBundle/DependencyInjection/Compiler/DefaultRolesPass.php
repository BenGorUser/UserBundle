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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Load default roles compiler pass.
 *
 * Based on configuration the routes are created dynamically.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DefaultRolesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');
        foreach ($config['user_class'] as $key => $user) {
            $roles = $user['class']::availableRoles();
            $defaultRoles = $user['default_roles'];
            if (count($defaultRoles) !== count(array_intersect($defaultRoles, $roles))) {
                throw new \InvalidArgumentException('Passed roles must be elements inside "availableRoles" method');
            }
            if (empty($defaultRoles)) {
                if (false === empty($roles)) {
                    $defaultRoles[] = $roles[0];
                }
            }

            $container->setParameter('bengor_user.' . $key . '_default_roles', array_unique($defaultRoles));
        }
    }
}
