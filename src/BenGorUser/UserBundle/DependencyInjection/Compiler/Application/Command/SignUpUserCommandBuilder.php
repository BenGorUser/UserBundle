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
    public function register($user, $isApi = false)
    {
        $command = $this->{$isApi ? $this->apiSpecification : $this->specification}($user)['command'];
        $handler = $this->{$isApi ? $this->apiSpecification : $this->specification}($user)['handler'];
        $handlerArguments = $this->{$isApi ? $this->apiSpecification : $this->specification}($user)['handlerArguments'];

        $this->registerCommandHandler($user, $handler, $handlerArguments, $command, $isApi);

        if ($this->specification !== 'defaultSpecification' && $this->apiSpecification !== 'defaultSpecification') {
            (new DefaultSignUpUserCommandBuilder($this->container, $this->persistence))->build($user);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build($user)
    {
        if (false === $this->enabled && false === $this->apiEnabled) {
            (new DefaultSignUpUserCommandBuilder($this->container, $this->persistence))->build($user);

            return $this->container;
        }

        return parent::build($user);
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
        if ('default' !== $specificationName
            && 'withConfirmation' !== $specificationName
            && 'byInvitation' !== $specificationName
        ) {
            throw new RuntimeException(
                'The sign up user types must be "default" or "with_confirmation" or "by_invitation"'
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
        return 'bengor_user.' . $user . '.sign_up';
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
                'bengor.user.infrastructure.domain.model.' . $user . '_factory_sign_up'
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
        (new ResendInvitationUserCommandBuilder($this->container, $this->persistence))->build($user);

        return [
            'command'          => ByInvitationSignUpUserCommand::class,
            'handler'          => ByInvitationSignUpUserHandler::class,
            'handlerArguments' => $this->invitationHandlerArguments($user),
        ];
    }

    private function registerCommandHandler($user, $handler, $handlerArguments, $command, $isApi = false)
    {
        $this->container->setDefinition(
            $this->definition($user, $isApi),
            (new Definition(
                $handler,
                $handlerArguments
            ))->addTag(
                $this->commandHandlerTag($user, $isApi), [
                    'handles' => $command,
                ]
            )
        );
    }
}
