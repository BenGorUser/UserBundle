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

use BenGorUser\User\Application\Command\PurgeOutdatedTokens\PurgeOutdatedRememberPasswordTokensUserCommand;
use BenGorUser\User\Application\Command\PurgeOutdatedTokens\PurgeOutdatedRememberPasswordTokensUserHandler;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Purge outdated remember password tokens user command builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class PurgeOutdatedRememberPasswordTokensUserCommandBuilder extends CommandBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                PurgeOutdatedRememberPasswordTokensUserHandler::class, [
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.persistence.' . $user . '_repository'
                    ),
                ]
            ))->addTag(
                'bengor_user_' . $user . '_command_bus_handler', [
                    'handles' => PurgeOutdatedRememberPasswordTokensUserCommand::class,
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.command.purge_outdated_' . $user . '_remember_password_tokens';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.' . $user . '.purge_outdated_remember_password_tokens';
    }
}
