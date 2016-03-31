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

namespace BenGor\UserBundle\DependencyInjection\Compiler;

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
class CommandsServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $container->findDefinition('bengor.user_bundle.command.sign_up_' . $key . '_command')
                ->setArguments([
                    $container->getDefinition(
                        'bengor.user.application.service.sign_up_' . $key . '_transactional'
                    ),
                    $key,
                    $user['class'],
                ]);
            $container->findDefinition('bengor.user_bundle.command.enable_' . $key . '_command')
                ->setArguments([
                    $container->getDefinition(
                        'bengor.user.application.service.enable_' . $key . '_transactional'
                    ),
                    $key,
                ]);
        }
    }
}
