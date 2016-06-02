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

use BenGorUser\User\Infrastructure\Domain\Model\UserFactoryInvite;
use BenGorUser\User\Infrastructure\Domain\Model\UserFactorySignUp;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Registers domain model services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DomainServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $container->setDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory_sign_up',
                (new Definition(
                    UserFactorySignUp::class, [
                        $user['class'],
                    ]
                ))->setPublic(false)
            );
            $container->setAlias(
                'bengor_user.' . $key . '_factory_sign_up',
                'bengor.user.infrastructure.domain.model.' . $key . '_factory_sign_up'
            );

            $container->setDefinition(
                'bengor.user.infrastructure.domain.model.' . $key . '_factory_invite',
                (new Definition(
                    UserFactoryInvite::class, [
                        $user['class'],
                    ]
                ))->setPublic(false)
            );
            $container->setAlias(
                'bengor_user.' . $key . '_factory_invite',
                'bengor.user.infrastructure.domain.model.' . $key . '_factory_invite'
            );
        }
    }
}
