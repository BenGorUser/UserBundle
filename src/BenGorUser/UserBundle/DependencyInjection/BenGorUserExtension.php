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
use BenGorUser\UserBundle\Command\PurgeOutdatedInvitationTokensCommand;
use BenGorUser\UserBundle\Command\PurgeOutdatedRememberPasswordTokensCommand;
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

        $loader->load('forms.yml');
        $loader->load('routing.yml');

        $container->setParameter('bengor_user.config', $config);

        foreach ($config['user_class'] as $key => $user) {
            $this->loadCommands($container, $key);
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

        $container->setDefinition(
            'bengor.user.command.purge_outdated_' . $user . '_invitations_tokens_command',
            (new Definition(PurgeOutdatedInvitationTokensCommand::class))->addTag('console.command')
        );

        $container->setDefinition(
            'bengor.user.command.purge_outdated_' . $user . '_remember_password_tokens_command',
            (new Definition(PurgeOutdatedRememberPasswordTokensCommand::class))->addTag('console.command')
        );
    }
}
