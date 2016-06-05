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

namespace BenGorUser\UserBundle\Routing;

use BenGorUser\UserBundle\Form\Type\SignUpByInvitationType;
use BenGorUser\UserBundle\Form\Type\SignUpByInvitationWithConfirmationType;
use BenGorUser\UserBundle\Form\Type\SignUpType;
use BenGorUser\UserBundle\Form\Type\SignUpWithConfirmationType;
use Symfony\Component\Routing\Route;

/**
 * Sing up user routes loader class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpRoutesLoader extends RoutesLoader
{
    /**
     * The fully qualified class name of formType.
     *
     * @var string
     */
    private $formType;

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user_sign_up' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function register($user, array $config)
    {
        $this->routes->add($config['name'], new Route(
            $config['path'],
            [
                '_controller' => 'BenGorUserBundle:SignUp:' . $config['type'],
                'userClass'   => $user,
                'firewall'    => $config['firewall'],
                'formType'    => $this->formType,
            ],
            [],
            [],
            '',
            [],
            ['GET', 'POST']
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitize($specificationName)
    {
        if ('by_invitation' === $specificationName
            || 'byInvitation' === $specificationName
        ) {
            $this->formType = SignUpByInvitationType::class;

            return 'byInvitation';
        }
        if ('by_invitation_with_confirmation' === $specificationName
            || 'byInvitationWithConfirmation' === $specificationName
        ) {
            $this->formType = SignUpByInvitationWithConfirmationType::class;

            return 'byInvitation';
        }
        if ('default' === $specificationName) {
            $this->formType = SignUpType::class;

            return 'default';
        }
        if ('with_confirmation' === $specificationName
            || 'withConfirmation' === $specificationName
        ) {
            $this->formType = SignUpWithConfirmationType::class;

            return 'default';
        }
        throw new \RuntimeException('Given sign up type is not supported');
    }
}
