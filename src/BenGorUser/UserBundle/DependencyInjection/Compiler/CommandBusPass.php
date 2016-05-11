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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler;

use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Command bus pass.
 *
 * @author Gorka Laucirica <gorka.lauzirila@gmail.com>
 */
class CommandBusPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $container->setDefinition(
                'bengor.user.command_bus.' . $key,
                (new Definition(
                    MessageBusSupportingMiddleware::class
                ))->addTag('message_bus', [
                    'type' => 'command',
                    'middleware_tag' => 'bengor.user.command_bus.middleware.'. $key
                ])->setPublic(false)
            );
        }
    }
}
