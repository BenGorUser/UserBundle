<?php

/*
 * This file is part of the BenGorUserBundle bundle.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service;

use BenGorUser\User\Application\Service\ChangePassword\ByRequestRememberPasswordChangeUserPasswordSpecification;
use BenGorUser\User\Application\Service\ChangePassword\ChangeUserPasswordHandler;
use BenGorUser\User\Application\Service\ChangePassword\DefaultChangeUserPasswordSpecification;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Change user password service builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangeUserPasswordServiceBuilder extends ServiceBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        (new ByEmailWithoutOldPasswordChangeUserPasswordServiceBuilder(
            $this->container, $this->persistence
        ))->build($user);

        $this->container->setDefinition(
            $this->definitionName($user),
            new Definition(
                ChangeUserPasswordHandler::class, [
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.persistence.' . $user . '_repository'
                    ),
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.security.symfony.' . $user . '_password_encoder'
                    ),
                    $this->{$this->specification}($user),
                ]
            )
        );
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
        return 'bengor.user.application.service.change_' . $user . '_password';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.change_' . $user . '_password';
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
        return new Definition(
            DefaultChangeUserPasswordSpecification::class, [
                $this->container->getDefinition(
                    'bengor.user.infrastructure.persistence.' . $user . '_repository'
                ),
                $this->container->getDefinition(
                    'bengor.user.infrastructure.security.symfony.' . $user . '_password_encoder'
                ),
            ]
        );
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
        (new RequestRememberPasswordServiceBuilder($this->container, $this->persistence))->build($user);

        return new Definition(
            ByRequestRememberPasswordChangeUserPasswordSpecification::class, [
                $this->container->getDefinition(
                    'bengor.user.infrastructure.persistence.' . $user . '_repository'
                ),
            ]
        );
    }
}
