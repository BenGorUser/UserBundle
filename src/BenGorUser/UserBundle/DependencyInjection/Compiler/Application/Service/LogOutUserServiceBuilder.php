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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service;

use BenGorUser\User\Application\Service\LogOut\LogOutUserCommand;
use BenGorUser\User\Application\Service\LogOut\LogOutUserHandler;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Log out user service builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class LogOutUserServiceBuilder extends ServiceBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                LogOutUserHandler::class, [
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.persistence.' . $user . '_repository'
                    ),
                ]
            ))->addTag('bengor_user_' . $user . '_command_bus_handler', [
                'handles' => LogOutUserCommand::class,
            ])
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.service.log_out_' . $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.log_out_' . $user;
    }
}
