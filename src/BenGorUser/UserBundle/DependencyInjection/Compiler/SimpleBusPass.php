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

use BenGorUser\UserBundle\CommandBus\SimpleBusUserCommandBus;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterMessageRecorders;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Simple bus pass.
 *
 * @author Gorka Laucirica <gorka.lauzirila@gmail.com>
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SimpleBusPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $busId = 'bengor.user.simple_bus_' . $key . '_command_bus';
            $middlewareTag = 'bengor_user_' . $key . '_command_bus_middleware';

            $container->setDefinition(
                $busId,
                (new Definition(
                    MessageBusSupportingMiddleware::class
                ))->addTag('message_bus', [
                    'type'           => 'command',
                    'middleware_tag' => $middlewareTag,
                ])->setPublic(false)
            );
            (new ConfigureMiddlewares($busId, $middlewareTag))->process($container);
            (new RegisterHandlers(
                'simple_bus.command_bus.command_handler_map',
                'bengor_user_' . $key . '_command_bus_handler',
                'handles'
            ))->process($container);
            (new RegisterMessageRecorders(
                'simple_bus.event_bus.aggregates_recorded_messages',
                'event_recorder'
            ))->process($container);

            $container->setDefinition(
                'bengor.user.' . $key . '_command_bus',
                new Definition(
                    SimpleBusUserCommandBus::class, [
                        $container->getDefinition($busId),
                    ]
                )
            );
        }
    }
}
