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

use BenGor\File\Infrastructure\Application\Service\SqlSession;
use BenGor\User\Infrastructure\Application\Service\DoctrineODMMongoDBSession;
use BenGor\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\DoctrineODMMongoDBUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\DoctrineODMMongoDBUserRepository;
use BenGor\User\Infrastructure\Persistence\Doctrine\ORM\DoctrineORMUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\Doctrine\ORM\DoctrineORMUserRepository;
use BenGor\User\Infrastructure\Persistence\Sql\SqlUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\Sql\SqlUserRepository;
use Ddd\Infrastructure\Application\Service\DoctrineSession;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register persistence services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class PersistenceServicesPass implements CompilerPassInterface
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

            if ('doctrine_orm' === $user['persistence']) {
                if (!$container->hasDefinition('doctrine.orm.default_entity_manager')) {
                    throw new RuntimeException(
                        'When the persistence layer is "doctrine_orm" requires ' .
                        'the installation and set up of the DoctrineBundle'
                    );
                }
                $this->loadDoctrineOrm($container, $key, $user, $guestClass);
            } elseif ('doctrine_odm_mongodb' === $user['persistence']) {
                if (!$container->hasDefinition('doctrine_mongodb.odm.document_manager')) {
                    throw new RuntimeException(
                        'When the persistence layer is "doctrine_odm_mongodb" requires ' .
                        'the installation and set up of the DoctrineMongoDBBundle'
                    );
                }
                $this->loadDoctrineOdmMongoDB($container, $key, $user, $guestClass);
            } elseif ('sql' === $user['persistence']) {
                $this->loadSql($container, $key, $user, $guestClass);
            }

            $container->setAlias(
                'bengor_user.' . $key . '_repository',
                'bengor.user.infrastructure.persistence.' . $key . '_repository'
            );
            if (null !== $guestClass) {
                $container->setAlias(
                    'bengor_user.' . $key . '_guest_repository',
                    'bengor.user.infrastructure.persistence.' . $key . '_guest_repository'
                );
            }
        }
    }

    /**
     * Loads the Doctrine ORM repository related services.
     *
     * @param ContainerBuilder $container  The container builder
     * @param string           $key        The name of file type
     * @param array            $user       User configuration tree
     * @param string           $guestClass FQCN about user guest class
     */
    private function loadDoctrineOrm(ContainerBuilder $container, $key, $user, $guestClass)
    {
        $container->register(
            'bengor.user.infrastructure.application.service.doctrine_orm_session',
            DoctrineSession::class
        )->addArgument(
            new Reference('doctrine.orm.default_entity_manager')
        )->setPublic(false);

        $container->setDefinition(
            'bengor.user.infrastructure.persistence.' . $key . '_repository',
            (new Definition(
                DoctrineORMUserRepository::class, [
                    $user['class'],
                ]
            ))->setFactory([
                new Reference('doctrine.orm.default_entity_manager'), 'getRepository',
            ])->setPublic(false)
        );

        if (null !== $guestClass) {
            $container->setDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_guest_repository',
                (new Definition(
                    DoctrineORMUserGuestRepository::class, [
                        $guestClass,
                    ]
                ))->setFactory([
                    new Reference('doctrine.orm.default_entity_manager'), 'getRepository',
                ])->setPublic(false)
            );
        }
    }

    /**
     * Loads the Doctrine ODM MongoDB related services.
     *
     * @param ContainerBuilder $container  The container builder
     * @param string           $key        The name of file type
     * @param array            $user       User configuration tree
     * @param string           $guestClass FQCN about user guest class
     */
    private function loadDoctrineOdmMongoDB(ContainerBuilder $container, $key, $user, $guestClass)
    {
        $container->register(
            'bengor.user.infrastructure.application.service.doctrine_odm_mongodb_session',
            DoctrineODMMongoDBSession::class
        )->addArgument(
            new Reference('doctrine_mongodb.odm.document_manager')
        )->setPublic(false);

        $container->setDefinition(
            'bengor.user.infrastructure.persistence.' . $key . '_repository',
            (new Definition(
                DoctrineODMMongoDBUserRepository::class, [
                    $user['class'],
                ]
            ))->setFactory([
                new Reference('doctrine.odm.mongodb.document_manager'), 'getRepository',
            ])->setPublic(false)
        );

        if (null !== $guestClass) {
            $container->setDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_guest_repository',
                (new Definition(
                    DoctrineODMMongoDBUserGuestRepository::class, [
                        $guestClass,
                    ]
                ))->setFactory([
                    new Reference('doctrine.odm.mongodb.document_manager'), 'getRepository',
                ])->setPublic(false)
            );
        }
    }

    /**
     * Loads the SQL repository related services.
     *
     * @param ContainerBuilder $container  The container builder
     * @param string           $key        The name of file type
     * @param array            $user       User configuration tree
     * @param string           $guestClass FQCN about user guest class
     */
    private function loadSql(ContainerBuilder $container, $key, $user, $guestClass)
    {
        $container->register(
            'bengor.user.infrastructure.application.service.sql_session',
            SqlSession::class
        )->addArgument(
            new Reference('pdo')
        )->setPublic(false);

        $container->setDefinition(
            'pdo',
            (new Definition(
                \PDO::class, [
                    'mysql:dbname=' . $container->getParameter('database_name'),
                    $container->getParameter('database_user'),
                    $container->getParameter('database_password'),
                ]
            ))->setPublic(false)
        );
        $container->setDefinition(
            'bengor.user.infrastructure.persistence.' . $key . '_repository',
            (new Definition(
                SqlUserRepository::class, [
                    new Reference('pdo'),
                ]
            ))->setPublic(false)
        );

        if (null !== $guestClass) {
            $container->setDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_guest_repository',
                (new Definition(
                    SqlUserGuestRepository::class, [
                        new Reference('pdo'),
                    ]
                ))->setPublic(false)
            );
        }
    }
}
