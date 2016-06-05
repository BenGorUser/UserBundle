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
use BenGorUser\UserBundle\EventBus\SimpleBusUserEventBus;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\CallableMap;
use SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver;
use SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver;
use SimpleBus\Message\Name\ClassBasedNameResolver;
use SimpleBus\Message\Recorder\AggregatesRecordedMessages;
use SimpleBus\Message\Subscriber\Resolver\NameBasedMessageSubscriberResolver;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\AddMiddlewareTags;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\CompilerPassUtil;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterMessageRecorders;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterSubscribers;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
            $this->commandBus($container, $key);
            $this->eventBus($container, $key);
        }
    }

    /**
     * Registers the command bus for given user.
     *
     * @param ContainerBuilder $container The container builder
     * @param string           $user      The user name
     */
    private function commandBus(ContainerBuilder $container, $user)
    {
        $busId = 'bengor.user.simple_bus_' . $user . '_command_bus';
        $middlewareTag = 'bengor_user_' . $user . '_command_bus_middleware';
        $handlerTag = 'bengor_user_' . $user . '_command_bus_handler';

        // Define the command bus for the given user type
        // The middleware tag string will be later used to add
        // required middleware to this specific command bus
        $container->setDefinition(
            $busId,
            (new Definition(
                MessageBusSupportingMiddleware::class
            ))->addTag('message_bus', [
                'type'           => 'command',
                'middleware_tag' => $middlewareTag,
            ])->setPublic(false)
        );

        // Find services tagged with $middlewareTag string and add
        // them to the current user type's command bus
        (new ConfigureMiddlewares($busId, $middlewareTag))->process($container);

        // Declares callable resolver for the current user type's command bus
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_command_bus.callable_resolver',
            (new Definition(
                ServiceLocatorAwareCallableResolver::class, [
                    [
                        new Reference('service_container'), 'get',
                    ],
                ]
            ))->setPublic(false)
        );

        // Declares class based command name resolver for the current user type's command bus
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_command_bus.class_based_command_name_resolver',
            (new Definition(
                ClassBasedNameResolver::class
            ))->setPublic(false)
        );

        // Declares the handler map for the current user type's command bus,
        // will contain the association between commands an handlers
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_command_bus.command_handler_map',
            (new Definition(
                CallableMap::class, [
                    [],
                    $container->getDefinition('bengor_user.simple_bus.' . $user . '_command_bus.callable_resolver'),
                ]
            ))->setPublic(false)
        );

        // Declares the handler resolver with the NameResolver
        // strategy and HandlerMap key values for Command => Handler
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_command_bus.command_handler_resolver',
            (new Definition(
                NameBasedMessageHandlerResolver::class, [
                    $container->getDefinition(
                        'bengor_user.simple_bus.' . $user . '_command_bus.class_based_command_name_resolver'
                    ),
                    $container->getDefinition(
                        'bengor_user.simple_bus.' . $user . '_command_bus.command_handler_map'
                    ),
                ]
            ))->setPublic(false)
        );

        // Declares the Handler
        $container
            ->findDefinition('bengor_user.simple_bus.' . $user . '_command_bus.delegates_to_message_handler_middleware')
            ->addArgument(
                $container->getDefinition('bengor_user.simple_bus.' . $user . '_command_bus.command_handler_resolver')
            )->setPublic(false);

        // Declares the tag that will be used to associate the
        // handlers to the current user type's command bus
        (new RegisterHandlers(
            'bengor_user.simple_bus.' . $user . '_command_bus.command_handler_map',
            $handlerTag,
            'handles'
        ))->process($container);

        // Decorate SimpleBus' command bus with BenGorUser's command bus
        $container->setDefinition(
            'bengor_user.' . $user . '_command_bus',
            new Definition(
                SimpleBusUserCommandBus::class, [
                    $container->getDefinition($busId),
                ]
            )
        );
    }

    /**
     * Registers the event bus for given user.
     *
     * @param ContainerBuilder $container The container builder
     * @param string           $user      The user name
     */
    private function eventBus(ContainerBuilder $container, $user)
    {
        $busId = 'bengor.user.simple_bus_' . $user . '_event_bus';
        $middlewareTag = 'bengor_user_' . $user . '_event_bus_middleware';
        $subscriberTag = 'bengor_user_' . $user . '_event_subscriber';

        // Define the event bus for the given user type
        // The middleware tag string will be later used to add
        // required middleware to this specific event bus
        $container->setDefinition(
            $busId,
            (new Definition(
                MessageBusSupportingMiddleware::class
            ))->addTag('message_bus', [
                'type'           => 'event',
                'middleware_tag' => $middlewareTag,
            ])->setPublic(false)
        );

        // Find services tagged with $middlewareTag string and add
        // them to the current user type's event bus
        (new ConfigureMiddlewares($busId, $middlewareTag))->process($container);

        // Declares callable resolver for the current user type's event bus
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_event_bus.callable_resolver',
            (new Definition(
                ServiceLocatorAwareCallableResolver::class, [
                    [
                        new Reference('service_container'), 'get',
                    ],
                ]
            ))->setPublic(false)
        );

        // Declares class based event name resolver for the current user type's event bus
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_event_bus.class_based_event_name_resolver',
            (new Definition(
                ClassBasedNameResolver::class
            ))->setPublic(false)
        );

        // Declares the event subscribers collection for the current user type's event bus,
        // will contain the association between events an subscribers
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_event_bus.event_subscribers_collection',
            (new Definition(
                CallableCollection::class, [
                    [],
                    $container->getDefinition('bengor_user.simple_bus.' . $user . '_event_bus.callable_resolver'),
                ]
            ))->setPublic(false)
        );

        // Declares the subscriber resolver with the NameResolver
        // strategy and SubscriberCollection key values for Event => Subscriber
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_event_bus.event_subscribers_resolver',
            (new Definition(
                NameBasedMessageSubscriberResolver::class, [
                    $container->getDefinition(
                        'bengor_user.simple_bus.' . $user . '_event_bus.class_based_event_name_resolver'
                    ),
                    $container->getDefinition(
                        'bengor_user.simple_bus.' . $user . '_event_bus.event_subscribers_collection'
                    ),
                ]
            ))->setPublic(false)
        );

        // Declares the Subscriber
        $container
            ->findDefinition('bengor_user.simple_bus.' . $user . '_event_bus.delegates_to_message_handler_middleware')
            ->addArgument(
                $container->getDefinition('bengor_user.simple_bus.' . $user . '_event_bus.event_subscribers_resolver')
            )->setPublic(false);

        // Declares the tag that will be used to associate the
        // subscribers to the current user type's event bus
        (new RegisterSubscribers(
            'bengor_user.simple_bus.' . $user . '_event_bus.event_subscribers_collection',
            $subscriberTag,
            'subscribes_to'
        ))->process($container);

        // Decorate SimpleBus' event bus with BenGorUser's event bus
        $container->setDefinition(
            'bengor_user.' . $user . '_event_bus',
            new Definition(
                SimpleBusUserEventBus::class, [
                    $container->getDefinition($busId),
                ]
            )
        );

        // All about aggregate recorded message
        $container->setDefinition(
            'bengor_user.simple_bus.' . $user . '_event_bus.aggregates_recorded_messages',
            new Definition(
                AggregatesRecordedMessages::class, [
                    [],
                ]
            )
        )->setPublic(false);
        $container
            ->findDefinition('bengor_user.simple_bus.' . $user . '_event_bus.handles_recorded_messages_middleware')
            ->setArguments([
                $container->getDefinition(
                    'bengor_user.simple_bus.' . $user . '_event_bus.aggregates_recorded_messages'
                ),
                $container->getDefinition($busId),
            ])->setPublic(false);
        (new RegisterMessageRecorders(
            'bengor_user.simple_bus.' . $user . '_event_bus.aggregates_recorded_messages',
            'event_recorder'
        ))->process($container);

        CompilerPassUtil::prependBeforeOptimizationPass(
            $container,
            new AddMiddlewareTags(
                'bengor_user.simple_bus.' . $user . '_event_bus.handles_recorded_messages_middleware',
                ['command'],
                200
            )
        );
    }
}
