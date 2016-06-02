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

use BenGorUser\User\Application\Command\SignUp\ByInvitationSignUpUserCommand;
use BenGorUser\User\Application\Command\SignUp\ByInvitationSignUpUserHandler;
use BenGorUser\User\Application\Command\SignUp\ByInvitationWithConfirmationSignUpUserCommand;
use BenGorUser\User\Application\Command\SignUp\ByInvitationWithConfirmationSignUpUserHandler;
use BenGorUser\User\Application\Command\SignUp\SignUpUserCommand;
use BenGorUser\User\Application\Command\SignUp\SignUpUserHandler;
use BenGorUser\User\Application\Command\SignUp\WithConfirmationSignUpUserCommand;
use BenGorUser\User\Application\Command\SignUp\WithConfirmationSignUpUserHandler;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Sign up user command builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpUserCommandBuilder extends CommandBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $command = $this->{$this->specification}($user)['command'];
        $handler = $this->{$this->specification}($user)['handler'];
        $handlerArguments = $this->{$this->specification}($user)['handlerArguments'];

        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                $handler, $handlerArguments)
            )->addTag('bengor_user_' . $user . '_command_bus_handler', [
                    'handles' => $command,
                ]
            )->setPublic(false)
        );

        if ($this->specification !== 'defaultSpecification') {
            (new DefaultSignUpUserCommandBuilder($this->container, $this->persistence))->build($user);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize($specificationName)
    {
        if ('default' === $specificationName) {
            return 'defaultSpecification';
        }
        if ('with_confirmation' === $specificationName) {
            return 'withConfirmationSpecification';
        }
        if ('by_invitation' === $specificationName) {
            return 'byInvitationSpecification';
        }
        if ('by_invitation_with_confirmation' === $specificationName) {
            return 'byInvitationWithConfirmationSpecification';
        }
        if ('default' !== $specificationName
            && 'withConfirmation' !== $specificationName
            && 'byInvitation' !== $specificationName
            && 'byInvitationWithConfirmation' !== $specificationName
        ) {
            throw new RuntimeException(
                'The sign up user types must be "default" or "with_confirmation"' .
                'or "by_invitation" or "by_invitation_with_confirmation"'
            );
        }

        return $specificationName . 'Specification';
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.command.sign_up_' . $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.sign_up_' . $user;
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
            $this->container->getDefinition(
                'bengor.user.infrastructure.domain.model.' . $user . '_factory'
            ),
        ];
    }

    /**
     * Gets the invitation type handlers arguments to inject in the constructor.
     *
     * @param string $user The user name
     *
     * @return array
     */
    private function invitationHandlerArguments($user)
    {
        return array_merge($this->handlerArguments($user), [
            $this->container->getDefinition(
                'bengor.user.infrastructure.persistence.' . $user . '_guest_repository'
            ),
        ]);
    }

    /**
     * Gets the "default" specification.
     *
     * @param string $user The user name
     *
     * @return array
     */
    protected function defaultSpecification($user)
    {
        return [
            'command'          => SignUpUserCommand::class,
            'handler'          => SignUpUserHandler::class,
            'handlerArguments' => $this->handlerArguments($user),
        ];
    }

    /**
     * Gets the "with confirmation" specification.
     *
     * @param string $user The user name
     *
     * @return array
     */
    private function withConfirmationSpecification($user)
    {
        (new EnableUserCommandBuilder($this->container, $this->persistence))->build($user);

        return [
            'command'          => WithConfirmationSignUpUserCommand::class,
            'handler'          => WithConfirmationSignUpUserHandler::class,
            'handlerArguments' => $this->handlerArguments($user),
        ];
    }

    /**
     * Gets the "by invitation" specification.
     *
     * @param string $user The user name
     *
     * @return array
     */
    private function byInvitationSpecification($user)
    {
        (new InviteUserCommandBuilder($this->container, $this->persistence))->build($user);

        return [
            'command'          => ByInvitationSignUpUserCommand::class,
            'handler'          => ByInvitationSignUpUserHandler::class,
            'handlerArguments' => $this->invitationHandlerArguments($user),
        ];
    }

    /**
     * Gets the "by invitation with confirmation" specification.
     *
     * @param string $user The user name
     *
     * @return array
     */
    private function byInvitationWithConfirmationSpecification($user)
    {
        (new EnableUserCommandBuilder($this->container, $this->persistence))->build($user);
        (new InviteUserCommandBuilder($this->container, $this->persistence))->build($user);

        return [
            'command'          => ByInvitationWithConfirmationSignUpUserCommand::class,
            'handler'          => ByInvitationWithConfirmationSignUpUserHandler::class,
            'handlerArguments' => $this->invitationHandlerArguments($user),
        ];
    }
}