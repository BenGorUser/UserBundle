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

use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Command\ChangeUserPasswordCommandBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Command\LogInUserCommandBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Command\LogOutUserCommandBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Command\RemoveUserCommandBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Command\SignUpUserCommandBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Routing\SecurityRoutesLoaderBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register application commands compiler pass.
 *
 * Command declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationCommandsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            (new LogInUserCommandBuilder(
                $container, $user['persistence'], array_merge(
                    $user['use_cases']['security'], [
                        'routes' => (new SecurityRoutesLoaderBuilder(
                            $container, [
                                $key => $user['routes']['security'],
                            ]
                        ))->configuration(),
                    ]
                )
            ))->build($key);

            (new LogOutUserCommandBuilder(
                $container, $user['persistence'], $user['use_cases']['security']
            ))->build($key);

            (new SignUpUserCommandBuilder(
                $container, $user['persistence'], $user['use_cases']['sign_up']
            ))->build($key);

            (new ChangeUserPasswordCommandBuilder(
                $container, $user['persistence'], $user['use_cases']['change_password']
            ))->build($key);

            (new RemoveUserCommandBuilder(
                $container, $user['persistence'], $user['use_cases']['remove']
            ))->build($key);
        }
    }
}
