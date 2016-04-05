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

use BenGor\User\Infrastructure\Domain\Model\UserFactory;
use BenGor\User\Infrastructure\Domain\Model\UserGuestFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Register domain model services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DomainServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $container->setDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory',
                new Definition(
                    UserFactory::class, [
                        $user['class'],
                    ]
                )
            );
            $container->setAlias(
                'bengor_user.' . $key . '_factory',
                'bengor.user.infrastructure.domain.model.' . $key . '_factory'
            );

            if (class_exists($user['class'] . 'Guest')) {
                $container->setDefinition(
                    'bengor.user.infrastructure.domain.model.' . $key . '_guest_factory',
                    new Definition(
                        UserGuestFactory::class, [
                            $user['class'] . 'Guest',
                        ]
                    )
                );
                $container->setAlias(
                    'bengor_user.' . $key . '_guest_factory',
                    'bengor.user.infrastructure.domain.model.' . $key . '_guest_factory'
                );
            }
        }
    }
}
