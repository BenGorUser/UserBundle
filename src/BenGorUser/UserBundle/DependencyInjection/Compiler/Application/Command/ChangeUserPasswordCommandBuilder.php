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

use BenGorUser\User\Application\Command\ChangePassword\ByRequestRememberPasswordChangeUserPasswordCommand;
use BenGorUser\User\Application\Command\ChangePassword\ByRequestRememberPasswordChangeUserPasswordHandler;
use BenGorUser\User\Application\Command\ChangePassword\ChangeUserPasswordCommand;
use BenGorUser\User\Application\Command\ChangePassword\ChangeUserPasswordHandler;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Change user password command builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangeUserPasswordCommandBuilder extends CommandBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $command = $this->{$this->specification}($user)['command'];
        $handler = $this->{$this->specification}($user)['handler'];

        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                $handler, $this->handlerArguments($user)
            ))->addTag(
                'bengor_user_' . $user . '_command_bus_handler', [
                    'handles' => $command,
                ]
            )
        );

        (new WithoutOldPasswordChangeUserPasswordCommandBuilder(
            $this->container, $this->persistence
        ))->build($user);
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize($specificationName)
    {
        if ('by_request_remember_password' === $specificationName) {
            return 'byRequestRememberPasswordSpecification';
        }
        if ('default' !== $specificationName && 'byRequestRememberPassword' !== $specificationName) {
            throw new RuntimeException(
                'The change user password options must be "default" or "by_request_remember_password"'
            );
        }

        return $specificationName . 'Specification';
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.command.change_' . $user . '_password';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.change_' . $user . '_password';
    }

    /**
     * Gets the handler arguments to inject in the constructor.
     *
     * @param string $user The user name
     *
     * @return array
     */
    protected function handlerArguments($user)
    {
        return [
            $this->container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $user . '_repository'
            ),
            $this->container->getDefinition(
                'bengor.user.infrastructure.security.symfony.' . $user . '_password_encoder'
            ),
        ];
    }

    /**
     * Gets the "default" specification.
     *
     * @param string $user The user name
     *
     * @return Definition
     */
    private function defaultSpecification($user)
    {
        return [
            'command' => ChangeUserPasswordCommand::class,
            'handler' => ChangeUserPasswordHandler::class,
        ];
    }

    /**
     * Gets the "by request remember password" specification.
     *
     * @param string $user The user name
     *
     * @return Definition
     */
    private function byRequestRememberPasswordSpecification($user)
    {
        (new RequestRememberPasswordCommandBuilder($this->container, $this->persistence))->build($user);

        return [
            'command' => ByRequestRememberPasswordChangeUserPasswordCommand::class,
            'handler' => ByRequestRememberPasswordChangeUserPasswordHandler::class,
        ];
    }
}
