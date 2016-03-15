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

use BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserRepository;
use BenGor\User\Infrastructure\Persistence\Sql\SqlUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\Sql\SqlUserRepository;
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
class PersistenceServicesCompilerPass implements CompilerPassInterface
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

            if ('doctrine' === $user['persistence']
                && !$container->hasDefinition('doctrine.orm.default_entity_manager')
            ) {
                throw new RuntimeException(
                    'When the persistence layer is "doctrine" requires ' .
                    'the installation and set up of the DoctrineBundle'
                );
            }

            $method = sprintf('load%sRepository', ucfirst($user['persistence']));
            $this->$method($container, $key, $user, $guestClass);
        }
    }

    /**
     * Loads the Doctrine repository.
     *
     * @param ContainerBuilder $container  The container builder
     * @param string           $key        The name of file type
     * @param array            $user       User configuration tree
     * @param string           $guestClass FQCN about user guest class
     */
    private function loadDoctrineRepository(ContainerBuilder $container, $key, $user, $guestClass)
    {
        $container->setDefinition(
            'bengor.user.infrastructure.persistence.' . $key . '_repository',
            (new Definition(
                DoctrineUserRepository::class, [
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
                    DoctrineUserGuestRepository::class, [
                        $guestClass,
                    ]
                ))->setFactory([
                    new Reference('doctrine.orm.default_entity_manager'), 'getRepository'
                ])->setPublic(false)
            );
        }
    }

    /**
     * Loads the SQL repository.
     *
     * @param ContainerBuilder $container  The container builder
     * @param string           $key        The name of file type
     * @param array            $user       User configuration tree
     * @param string           $guestClass FQCN about user guest class
     */
    private function loadSqlRepository(ContainerBuilder $container, $key, $user, $guestClass)
    {
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
                        $guestClass,
                    ]
                ))->setPublic(false)
            );
        }
    }
}
