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

use BenGorUser\User\Application\Command\ChangePassword\WithoutOldPasswordChangeUserPasswordCommand;
use BenGorUser\User\Application\Command\ChangePassword\WithoutOldPasswordChangeUserPasswordHandler;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Without old password change user password command builder.
 *
 * Needed to solve the requirement about by email
 * change password specification as a Symfony command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class WithoutOldPasswordChangeUserPasswordCommandBuilder extends ChangeUserPasswordCommandBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                WithoutOldPasswordChangeUserPasswordHandler::class,
                $this->handlerArguments($user)
            ))->addTag(
                'bengor_user_' . $user . '_command_bus_handler', [
                    'handles' => WithoutOldPasswordChangeUserPasswordCommand::class,
                ]
            )->setPublic(false)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize($specificationName)
    {
        return 'withoutOldPasswordSpecification';
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.command.change_' . $user . '_password_without_old_password';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.change_' . $user . '_password_without_old_password';
    }
}
