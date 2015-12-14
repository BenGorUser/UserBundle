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

use BenGor\User\Domain\Model\Event\UserRegistered;
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
use BenGor\UserBundle\Model\User;
use Ddd\Domain\DomainEventPublisher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Populates domain event publisher compiler pass.
 *
 * Based on configuration the routes are created dynamically.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class PopulateEventPublisherCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('bengor.user.domain_event_publisher')) {
            return;
        }

        $definition = $container->getDefinition('bengor.user.domain_event_publisher');

        $taggedServices = $container->findTaggedServiceIds('bengor_user.subscriber');

        $definition->addMethodCall('subscribe', [
        ]);

//        var_dump($taggedServices);
        foreach ($taggedServices as $id => $tags) {
            //            var_dump($tags);
            var_dump($definition->getMethodCalls());
            $definition->addMethodCall('df45subscribe', [
                new Reference($id),
            ]);
        }

        $definition->
        $definition->addMethodCall('publish', [
            DomainEventPublisher::instance()->publish(
                new UserRegistered(
                    new User(
                        new UserId(),
                        new UserEmail('ben@ben.com'),
                        UserPassword::fromEncoded('123456', 'sdfvvf')
                    )
                )
            ), ]
        );
    }
}
