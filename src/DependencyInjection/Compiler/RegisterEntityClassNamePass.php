<?php

/*
 * This file is part of the User bundle.
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
 * Register entity class name pass class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegisterEntityClassNamePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bengor.user.infrastructure.domain.model.user_factory')
            && !$container->hasDefinition('bengor.user.infrastructure.persistence.doctrine.user_repository')
        ) {
            return;
        }
        $config = $container->getParameter('bengor_user.config');

        $entityClass = $config['domain']['model']['user']['class'];

        $container->getDefinition(
            'bengor.user.infrastructure.domain.model.user_factory'
        )->replaceArgument(0, $entityClass);
        $container->getDefinition(
            'bengor.user.infrastructure.persistence.doctrine.user_repository'
        )->replaceArgument(1, $entityClass);
    }
}
