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
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
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
        $loader->load('mailing.yml');
        $loader->load('routing.yml');

        $container->setParameter('bengor_user.config', $config);

        $this->loadCommands($container, $config);
    }

    /**
     * Loads commands as a service inside Symfony console.
     *
     * @param ContainerBuilder $container The container
     * @param array            $config    The bengor user configuration tree
     */
    private function loadCommands(ContainerBuilder $container, $config)
    {
        foreach ($config['user_class'] as $key => $user) {
            $container->addCompilerPass(
                new ConfigureMiddlewares(
                    'bengor.user.command_bus.' . $key,
                    'bengor.user.command_bus.middleware.' . $key
                )
            );

            $container->addCompilerPass(
                new RegisterHandlers(
                    'simple_bus.command_bus.command_handler_map',
                    'bengor.user.command_bus.handler.' . $key,
                    'handles'
                )
            );

            $container->setDefinition(
                'bengor.user.command.create_' . $key . '_command',
                (new Definition(CreateUserCommand::class))->addTag('console.command')
            );

            $container->setDefinition(
                'bengor.user.command.change_' . $key . '_password_command',
                (new Definition(ChangePasswordCommand::class))->addTag('console.command')
            );
        }
    }
}