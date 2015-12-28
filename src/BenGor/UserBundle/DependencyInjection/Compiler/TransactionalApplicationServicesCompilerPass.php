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

use BenGor\UserBundle\Security\FormLoginAuthenticator;
use Ddd\Application\Service\TransactionalApplicationService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register application transactional services compiler pass.
 *
 * The services are decorate with transactional
 * application service to simplify the use of them.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class TransactionalApplicationServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');

        foreach ($config['user_class'] as $key => $user) {
            $guestClass = null;
            if (class_exists($user['class'] . 'Guest')) {
                $guestClass = $user['class'] . 'Guest';
            }

            $container->register(
                'bengor.user.application.service.activate_' . $key . '_account_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.activate_' . $key . '_account')
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.change_' . $key . '_password_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.change_' . $key . '_password')
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.change_' . $key . '_password_using_remember_password_token_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.change_' . $key . '_password_using_remember_password_token')
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);
            if (null !== $guestClass) {
                $container->register(
                    'bengor.user.application.service.invite_' . $key . '_doctrine_transactional',
                    TransactionalApplicationService::class
                )->addArgument(
                    new Reference('bengor.user.application.service.invite_' . $key)
                )->addArgument(
                    new Reference('ddd.infrastructure.application.service.doctrine_session')
                )->setPublic(false);
            }
            $container->register(
                'bengor.user.application.service.log_in_' . $key . '_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.log_in_' . $key)
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.log_out_' . $key . '_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.log_out_' . $key)
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.remove_' . $key . '_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.remove_' . $key)
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.request_' . $key . '_remember_password_token_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.request_' . $key . '_remember_password_token')
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);
            if (null !== $guestClass) {
                $container->register(
                    'bengor.user.application.service.sign_up_' . $key . '_by_invitation_doctrine_transactional',
                    TransactionalApplicationService::class
                )->addArgument(
                    new Reference('bengor.user.application.service.sign_up_' . $key . '_by_invitation')
                )->addArgument(
                    new Reference('ddd.infrastructure.application.service.doctrine_session')
                )->setPublic(false);
            }
            $container->register(
                'bengor.user.application.service.sign_up_' . $key . '_doctrine_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.sign_up_' . $key)
            )->addArgument(
                new Reference('ddd.infrastructure.application.service.doctrine_session')
            )->setPublic(false);

            $container->setDefinition(
                'bengor.user_bundle.security.form_login_' . $key . '_authenticator_doctrine_transactional',
                new Definition(
                    FormLoginAuthenticator::class, [
                        $container->getDefinition(
                            'router.default'
                        ),
                        $container->getDefinition(
                            'bengor.user.application.service.log_in_' . $key . '_doctrine_transactional'
                        ),
                        $container->getDefinition(
                            'bengor.user.infrastructure.domain.model.' . $key . '_factory'
                        ),
                        $user['firewall']['pattern'],
                    ]
                )
            );
        }
    }
}
