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
    public function register($user, $isApi = false)
    {
        $this->container->setDefinition(
            $this->definition($user, $isApi),
            (new Definition(
                WithoutOldPasswordChangeUserPasswordHandler::class,
                $this->handlerArguments($user)
            ))->addTag(
                $this->commandHandlerTag($user, $isApi), [
                    'handles' => WithoutOldPasswordChangeUserPasswordCommand::class,
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build($user)
    {
        if (true === $this->enabled) {
            $this->doBuild($user);
        }
        if (true === $this->apiEnabled) {
            $this->doBuild($user, true);
        }

        return $this->container;
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
        return 'bengor_user.' . $user . '.change_password_without_old_password';
    }
}
