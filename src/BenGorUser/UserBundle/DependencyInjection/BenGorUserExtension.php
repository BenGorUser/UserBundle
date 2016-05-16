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

namespace BenGorUser\UserBundle\DependencyInjection;

use BenGorUser\UserBundle\Command\ChangePasswordCommand;
use BenGorUser\UserBundle\Command\CreateUserCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * BenGor user bundle extension class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class BenGorUserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/services'));

        $loader->load('event_subscribers.yml');
        $loader->load('forms.yml');
        $loader->load('routing.yml');
        $loader->load('simple_bus.yml');

        $container->setParameter('bengor_user.config', $config);

        foreach ($config['user_class'] as $key => $user) {
            $this->loadCommands($container, $key);
            $this->addMiddlewareTags($container, $key);
        }
    }

    /**
     * Loads commands as a service inside Symfony console.
     *
     * @param ContainerBuilder $container The container
     * @param string           $user      The user name
     */
    private function loadCommands(ContainerBuilder $container, $user)
    {
        $container->setDefinition(
            'bengor.user.command.create_' . $user . '_command',
            (new Definition(CreateUserCommand::class))->addTag('console.command')
        );

        $container->setDefinition(
            'bengor.user.command.change_' . $user . '_password_command',
            (new Definition(ChangePasswordCommand::class))->addTag('console.command')
        );
    }

    /**
     * Adds tags to Simple bus middlewares.
     *
     * @param ContainerBuilder $container The container
     * @param string           $user      The user name
     */
    private function addMiddlewareTags(ContainerBuilder $container, $user)
    {
        $container->getDefinition(
            'bengor_user.simple_bus.delegates_to_message_handler_middleware'
        )->addTag(
            'bengor_user_' . $user . '_command_bus_middleware', ['priority' => '-1000']
        );
        $container->getDefinition(
            'bengor_user.simple_bus.finishes_command_before_handling_next_middleware'
        )->addTag(
            'bengor_user_' . $user . '_command_bus_middleware', ['priority' => '1000']
        );
        $container->getDefinition(
            'bengor_user.simple_bus.doctrine_transactional_middleware'
        )->addTag(
            'bengor_user_' . $user . '_command_bus_middleware', ['priority' => '0']
        );
        $container->getDefinition(
            'bengor_user.simple_bus.handles_recorded_messages_middleware'
        )->addTag(
            'bengor_user_' . $user . '_command_bus_middleware', ['priority' => '-100']
        );
    }
}
