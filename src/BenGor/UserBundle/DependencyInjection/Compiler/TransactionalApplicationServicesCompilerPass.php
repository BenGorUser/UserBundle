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

use BenGor\User\Infrastructure\Application\Service\SqlSession;
use Ddd\Application\Service\TransactionalApplicationService;
use Ddd\Infrastructure\Application\Service\DoctrineSession;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
            $method = sprintf('load%sSession', ucfirst($user['persistence']));
            $this->$method($container);

            $container->register(
                'bengor.user.application.service.activate_' . $key . '_account_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.activate_' . $key . '_account')
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.change_' . $key . '_password_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.change_' . $key . '_password')
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.change_' . $key . '_password_using_remember_password_token_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.change_' . $key . '_password_using_remember_password_token')
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
            if (null !== $guestClass) {
                $container->register(
                    'bengor.user.application.service.invite_' . $key . '_transactional',
                    TransactionalApplicationService::class
                )->addArgument(
                    new Reference('bengor.user.application.service.invite_' . $key)
                )->addArgument(
                    new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
                )->setPublic(false);
            }
            $container->register(
                'bengor.user.application.service.log_in_' . $key . '_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.log_in_' . $key)
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.log_out_' . $key . '_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.log_out_' . $key)
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.remove_' . $key . '_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.remove_' . $key)
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
            $container->register(
                'bengor.user.application.service.request_' . $key . '_remember_password_token_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.request_' . $key . '_remember_password_token')
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
            if (null !== $guestClass) {
                $container->register(
                    'bengor.user.application.service.sign_up_' . $key . '_by_invitation_transactional',
                    TransactionalApplicationService::class
                )->addArgument(
                    new Reference('bengor.user.application.service.sign_up_' . $key . '_by_invitation')
                )->addArgument(
                    new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
                )->setPublic(false);
            }
            $container->register(
                'bengor.user.application.service.sign_up_' . $key . '_transactional',
                TransactionalApplicationService::class
            )->addArgument(
                new Reference('bengor.user.application.service.sign_up_' . $key)
            )->addArgument(
                new Reference('bengor.user.infrastructure.application.service.' . $user['persistence'] . '_session')
            )->setPublic(false);
        }
    }

    /**
     * Loads the Doctrine session service.
     *
     * @param ContainerBuilder $container The container
     */
    private function loadDoctrineSession(ContainerBuilder $container)
    {
        $container->register(
            'bengor.user.infrastructure.application.service.doctrine_session',
            DoctrineSession::class
        )->addArgument(
            new Reference('doctrine.orm.default_entity_manager')
        )->setPublic(false);
    }

    /**
     * Loads the SQL session service.
     *
     * @param ContainerBuilder $container The container
     */
    private function loadSqlSession(ContainerBuilder $container)
    {
        $container->register(
            'bengor.user.infrastructure.application.service.sql_session',
            SqlSession::class
        )->addArgument(
            new Reference('pdo')
        )->setPublic(false);
    }
}
