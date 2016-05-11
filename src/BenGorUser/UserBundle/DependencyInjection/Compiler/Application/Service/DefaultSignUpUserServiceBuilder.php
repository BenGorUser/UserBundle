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

use BenGorUser\User\Application\Service\SignUp\SignUpUserCommand;
use BenGorUser\User\Application\Service\SignUp\SignUpUserHandler;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Default sign up user service builder.
 *
 * Needed to solve the requirement about default
 * sign up specification as a Symfony command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DefaultSignUpUserServiceBuilder extends SignUpUserServiceBuilder
{
    /**
     * {@inheritdoc}
     */
    public function register($user)
    {
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
                        'bengor.user.application.data_transformer.user_dto'
                    ),
                    $this->defaultSpecification($user),
                ]
            ))->addTag('bengor.user.command_bus.handler.'. $user, [
                'handles' => SignUpUserCommand::class
            ])
        );
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
        return 'bengor.user.application.service.sign_up_' . $user . '_default';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliasDefinitionName($user)
    {
        return 'bengor_user.sign_up_' . $user . '_default';
    }
}
