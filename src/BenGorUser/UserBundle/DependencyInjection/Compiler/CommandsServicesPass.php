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
 * Register Symfony commands as a service compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class CommandsServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $container->findDefinition('bengor.user.command.create_' . $key . '_command')
                ->setArguments([
                    $container->getDefinition(
                        'bengor.user.' . $key . '_command_bus'
                    ),
                    $key,
                    $user['class'],
                ]);

            $container->findDefinition('bengor.user.command.change_' . $key . '_password_command')
                ->setArguments([
                    $container->getDefinition(
                        'bengor.user.' . $key . '_command_bus'
                    ),
                    $key,
                ]);
        }
    }
}
