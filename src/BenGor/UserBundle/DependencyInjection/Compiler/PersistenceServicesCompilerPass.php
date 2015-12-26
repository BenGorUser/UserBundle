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

use BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register persistence services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class PersistenceServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $guestClass = null;
            if (class_exists($user['class'] . 'Guest')) {
                $guestClass = $user['class'] . 'Guest';
            }

            $container->setDefinition(
                'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository',
                (new Definition(
                    DoctrineUserRepository::class, [
                        $user['class'],
                    ]
                ))->setFactory([new Reference('doctrine.orm.default_entity_manager'), 'getRepository'])
            );
            if (null !== $guestClass) {
                $container->setDefinition(
                    'bengor.user.infrastructure.persistence.doctrine.' . $key . '_guest_repository',
                    new Definition(
                        DoctrineUserGuestRepository::class, [
                            $container->getDefinition('doctrine.orm.default_entity_manager'), $guestClass,
                        ]
                    )
                );
            }
        }
    }
}
