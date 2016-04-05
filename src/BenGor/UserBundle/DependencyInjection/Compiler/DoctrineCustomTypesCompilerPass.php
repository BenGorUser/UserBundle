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

use BenGor\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\Types\UserEmailType;
use BenGor\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\Types\UserPasswordType;
use BenGor\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\Types\UserRolesType as ODMMongoDBUserRolesType;
use BenGor\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\Types\UserTokenType;
use BenGor\User\Infrastructure\Persistence\Doctrine\ORM\Types\UserGuestIdType;
use BenGor\User\Infrastructure\Persistence\Doctrine\ORM\Types\UserIdType;
use BenGor\User\Infrastructure\Persistence\Doctrine\ORM\Types\UserRolesType;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Load Doctrine's custom types compiler pass.
 *
 * Based on configuration the routes are created dynamically.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DoctrineCustomTypesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->orm($container);
        $this->odmMongoDb();
    }

    /**
     * Loads the custom types of Doctrine ODM MongoDB.
     */
    private function odmMongoDb()
    {
        if (class_exists('Doctrine\\ODM\MongoDB\\Types\\Type')) {
            Type::registerType(
                'user_email',
                UserEmailType::class
            );
            Type::registerType(
                'user_guest_id',
                UserGuestIdType::class
            );
            Type::registerType(
                'user_id',
                UserIdType::class
            );
            Type::registerType(
                'user_password',
                UserPasswordType::class
            );
            Type::registerType(
                'user_roles',
                ODMMongoDBUserRolesType::class
            );
            Type::registerType(
                'user_token',
                UserTokenType::class
            );
        }
    }

    /**
     * Loads the custom types of Doctrine ORM.
     *
     * @param ContainerBuilder $container The container builder
     */
    private function orm(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine.dbal.connection_factory')) {
            return;
        }

        $customTypes = $container->getParameter('doctrine.dbal.connection_factory.types');
        $customTypes = array_merge($customTypes, [
            'user_id'       => [
                'class'     => UserIdType::class,
                'commented' => true,
            ],
            'user_guest_id' => [
                'class'     => UserGuestIdType::class,
                'commented' => true,
            ],
            'user_roles'    => [
                'class'     => UserRolesType::class,
                'commented' => true,
            ],
        ]);

        $container->setParameter('doctrine.dbal.connection_factory.types', $customTypes);
        $container->findDefinition('doctrine.dbal.connection_factory')
            ->replaceArgument(0, '%doctrine.dbal.connection_factory.types%');
    }
}
