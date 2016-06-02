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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Command;

use BenGorUser\User\Application\Command\LogOut\LogOutUserCommand;
use BenGorUser\User\Application\Command\LogOut\LogOutUserHandler;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Log out user command builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class LogOutUserCommandBuilder extends CommandBuilder
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
            ))->addTag(
                'bengor_user_' . $user . '_command_bus_handler', [
                    'handles' => LogOutUserCommand::class,
                ]
            )->setPublic(false)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.command.log_out_' . $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.log_out_' . $user;
    }
}
