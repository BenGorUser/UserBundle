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

namespace BenGor\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Alias Doctrine services compiler pass.
 *
 * In most cases, this bundles is going to be used in
 * conjunction with Doctrine and its transactionallity so,
 * this class adds more readable and concise aliases
 * for this kind of services.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class AliasServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        $aliasMap = [];

        foreach ($config['user_class'] as $key => $user) {
            $guestClass = null;
            if (class_exists($user['class'] . 'Guest')) {
                $guestClass = $user['class'] . 'Guest';
            }
            $aliasMap = array_merge([
                // INFRAESTRUCTURE
                'bengor_user.symfony_' . $key . '_password_encoder' => 'bengor.user.infrastructure.security.symfony.' . $key . '_password_encoder',
                'bengor_user.' . $key . '_factory' => 'bengor.user.infrastructure.domain.model.' . $key . '_factory',
                'bengor_user.doctrine_' . $key . '_repository' => 'bengor.user.infrastructure.persistence.doctrine.' . $key . '_repository',
                //APPLICATION SERVICES
                'bengor_user.activate_' . $key . '_account' => 'bengor.user.application.service.activate_' . $key . '_account_doctrine_transactional',
                'bengor_user.change_' . $key . '_password' => 'bengor.user.application.service.change_' . $key . '_password_doctrine_transactional',
                'bengor_user.change_' . $key . '_password_using_remember_password_token' => 'bengor.user.application.service.change_' . $key . '_password_using_remember_password_token_doctrine_transactional',
                'bengor_user.log_in_' . $key => 'bengor.user.application.service.log_in_' . $key . '_doctrine_transactional',
                'bengor_user.log_out_' . $key => 'bengor.user.application.service.log_out_' . $key . '_doctrine_transactional',
                'bengor_user.remove_' . $key => 'bengor.user.application.service.remove_' . $key . '_doctrine_transactional',
                'bengor_user.request_' . $key . '_remember_password_token' => 'bengor.user.application.service.request_' . $key . '_remember_password_token_doctrine_transactional',
                'bengor_user.sign_up_' . $key => 'bengor.user.application.service.sign_up_' . $key . '_doctrine_transactional',
                'bengor_user.form_login_' . $key . '_authenticator' => 'bengor.user_bundle.security.form_login_' . $key . '_authenticator'
            ], $aliasMap);
            if (null !== $guestClass) {
                //GUEST CLASSES
                $aliasMap = array_merge([
                    'bengor_user.doctrine_' . $key . '_guest_repository' => 'bengor.user.infrastructure.persistence.doctrine.' . $key . '_guest_repository',
                    'bengor_user.invite_' . $key => 'bengor.user.application.service.invite_' . $key . '_doctrine_transactional',
                    'bengor_user.sign_up_' . $key . '_by_invitation' => 'bengor.user.application.service.sign_up_' . $key . '_by_invitation_doctrine_transactional'
                ], $aliasMap);
            }
        }
        foreach($aliasMap as $alias => $id) {
            $container->setAlias($alias, $id);
        }
    }
}
