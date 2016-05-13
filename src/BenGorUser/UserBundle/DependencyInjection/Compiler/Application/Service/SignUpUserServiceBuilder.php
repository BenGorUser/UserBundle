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

use BenGorUser\User\Application\Service\SignUp\ByInvitationSignUpUserSpecification;
use BenGorUser\User\Application\Service\SignUp\ByInvitationWithConfirmationSignUpUserSpecification;
use BenGorUser\User\Application\Service\SignUp\DefaultSignUpUserSpecification;
use BenGorUser\User\Application\Service\SignUp\SignUpUserCommand;
use BenGorUser\User\Application\Service\SignUp\SignUpUserHandler;
use BenGorUser\User\Application\Service\SignUp\WithConfirmationSignUpUserSpecification;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Sign up user service builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpUserServiceBuilder extends ServiceBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        (new DefaultSignUpUserServiceBuilder($this->container, $this->persistence))->build($user);

        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                SignUpUserHandler::class, [
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.persistence.' . $user . '_repository'
                    ),
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.security.symfony.' . $user . '_password_encoder'
                    ),
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.domain.model.' . $user . '_factory'
                    ),
                    $this->container->getDefinition(
                        'bengor.user.application.data_transformer.user_no_transformation'
                    ),
                ]
            ))->addTag('bengor_user_' . $user . '_command_bus_handler', [
                'handles' => SignUpUserCommand::class
            ])
        );
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
        return 'bengor.user.application.service.sign_up_' . $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.sign_up_' . $user;
    }

    /**
     * Gets the "default" specification.
     *
     * @param string $user The user name
     *
     * @return Definition
     */
    protected function defaultSpecification($user)
    {
        return new Definition(
            DefaultSignUpUserSpecification::class
        );
    }

    /**
     * Gets the "with confirmation" specification.
     *
     * @param string $user The user name
     *
     * @return Definition
     */
    private function withConfirmationSpecification($user)
    {
        (new EnableUserServiceBuilder($this->container, $this->persistence))->build($user);

        return new Definition(
            WithConfirmationSignUpUserSpecification::class
        );
    }

    /**
     * Gets the "by invitation" specification.
     *
     * @param string $user The user name
     *
     * @return Definition
     */
    private function byInvitationSpecification($user)
    {
        (new InviteUserServiceBuilder($this->container, $this->persistence))->build($user);

        return new Definition(
            ByInvitationSignUpUserSpecification::class, [
                $this->container->getDefinition(
                    'bengor.user.infrastructure.persistence.' . $user . '_guest_repository'
                ),
            ]
        );
    }

    /**
     * Gets the "by invitation with confirmation" specification.
     *
     * @param string $user The user name
     *
     * @return Definition
     */
    private function byInvitationWithConfirmationSpecification($user)
    {
        (new EnableUserServiceBuilder($this->container, $this->persistence))->build($user);
        (new InviteUserServiceBuilder($this->container, $this->persistence))->build($user);

        return new Definition(
            ByInvitationWithConfirmationSignUpUserSpecification::class, [
                $this->container->getDefinition(
                    'bengor.user.infrastructure.persistence.' . $user . '_guest_repository'
                ),
            ]
        );
    }
}
