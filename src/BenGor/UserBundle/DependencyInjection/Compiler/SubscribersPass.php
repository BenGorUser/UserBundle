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
use Symfony\Component\DependencyInjection\Reference;

/**
 * Loads subscribers into event domain publisher compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SubscribersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(
            'bengor.user_bundle.event_listener.domain_event_publisher'
        );

        $taggedServices = $container->findTaggedServiceIds('bengor_user_subscriber');

        $references = [];
        foreach ($taggedServices as $id => $tags) {
            $references[] = new Reference($id);
        }
        $definition->replaceArgument(0, $references);
    }
}
