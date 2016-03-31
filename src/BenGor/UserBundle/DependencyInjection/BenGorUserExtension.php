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

namespace BenGor\UserBundle\DependencyInjection;

use BenGor\UserBundle\Command\EnableUserCommand;
use BenGor\UserBundle\Command\SignUpUserCommand;
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
            $container->setDefinition(
                'bengor.user_bundle.command.sign_up_' . $key . '_command',
                (new Definition(SignUpUserCommand::class))->addTag('console.command')
            );
            $container->setDefinition(
                'bengor.user_bundle.command.enable_' . $key . '_command',
                (new Definition(EnableUserCommand::class))->addTag('console.command')
            );
        }
    }
}
