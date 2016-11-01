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

use BenGorUser\User\Application\Command\SignUp\SignUpUserCommand;
use BenGorUser\User\Application\Command\SignUp\SignUpUserHandler;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Default sign up user command builder.
 *
 * Needed to solve the requirement about default
 * sign up specification as a Symfony command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DefaultSignUpUserCommandBuilder extends SignUpUserCommandBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                SignUpUserHandler::class, $this->handlerArguments($user)
            ))->addTag(
                'bengor_user_' . $user . '_command_bus_handler', [
                    'handles' => SignUpUserCommand::class,
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build($user)
    {
        $this->register($user);

        $this->container->setAlias(
            $this->aliasDefinitionName($user),
            $this->definitionName($user)
        );

        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize($specificationName)
    {
        return 'defaultSpecification';
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.command.sign_up_' . $user . '_default';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.' . $user . '.sign_up_default';
    }
}
