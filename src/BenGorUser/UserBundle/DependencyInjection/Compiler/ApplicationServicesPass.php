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

use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service\ChangeUserPasswordServiceBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service\LogInUserServiceBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service\LogOutUserServiceBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service\RemoveUserServiceBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service\SignUpUserServiceBuilder;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Routing\SecurityRoutesLoaderBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register application services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            (new LogInUserServiceBuilder(
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

            (new LogOutUserServiceBuilder(
                $container, $user['persistence'], $user['use_cases']['security']
            ))->build($key);

            (new SignUpUserServiceBuilder(
                $container, $user['persistence'], $user['use_cases']['sign_up']
            ))->build($key);

            (new ChangeUserPasswordServiceBuilder(
                $container, $user['persistence'], $user['use_cases']['change_password']
            ))->build($key);

            (new RemoveUserServiceBuilder(
                $container, $user['persistence'], $user['use_cases']['remove']
            ))->build($key);
        }
    }
}
