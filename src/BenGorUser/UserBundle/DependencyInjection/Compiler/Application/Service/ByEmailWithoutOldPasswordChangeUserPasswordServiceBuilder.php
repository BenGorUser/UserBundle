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

use BenGorUser\User\Application\Service\ChangePassword\ByEmailWithoutOldPasswordChangeUserPasswordSpecification;
use BenGorUser\User\Application\Service\ChangePassword\ChangeUserPasswordHandler;
use Symfony\Component\DependencyInjection\Definition;

/**
 * By email without old password change user password service builder.
 *
 * Needed to solve the requirement about by email
 * change password specification as a Symfony command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ByEmailWithoutOldPasswordChangeUserPasswordServiceBuilder extends ServiceBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
        $this->container->setDefinition(
            $this->definitionName($user),
            (new Definition(
                ChangeUserPasswordHandler::class, [
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.persistence.' . $user . '_repository'
                    ),
                    $this->container->getDefinition(
                        'bengor.user.infrastructure.security.symfony.' . $user . '_password_encoder'
                    ),
                    $this->byEmailWithoutOldPasswordSpecification($user),
                ]
            ))
        );
    }

    /**
     * Gets the "by email without confirmation" specification.
     *
     * @param string $user The user name
     *
     * @return Definition
     */
    private function byEmailWithoutOldPasswordSpecification($user)
    {
        return new Definition(
            ByEmailWithoutOldPasswordChangeUserPasswordSpecification::class, [
                $this->container->getDefinition(
                    'bengor.user.infrastructure.persistence.' . $user . '_repository'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize($specificationName)
    {
        return 'byEmailWithoutOldPasswordSpecification';
    }

    /**
     * {@inheritdoc}
     */
    protected function definitionName($user)
    {
        return 'bengor.user.application.service.change_' . $user . '_password_by_email';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.change_' . $user . '_password_by_email';
    }
}
