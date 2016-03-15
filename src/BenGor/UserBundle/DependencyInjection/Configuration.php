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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * BenGor user bundle configuration class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('ben_gor_user')
            ->children()
                ->arrayNode('user_class')->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('class')
                            ->isRequired(true)
                        ->end()
                        ->scalarNode('persistence')
                            ->defaultValue('doctrine')
                            ->validate()
                            ->ifNotInArray(['doctrine', 'sql'])
                                ->thenInvalid('Invalid persistence layer "%s"')
                            ->end()
                        ->end()
                        ->arrayNode('default_roles')
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('firewall')
                            ->isRequired(true)
                        ->end()
                        ->arrayNode('routes')->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('security')->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('enabled')
                                            ->defaultValue(true)
                                        ->end()
                                        ->arrayNode('login')->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('name')
                                                    ->defaultValue(null)
                                                ->end()
                                                ->scalarNode('path')
                                                    ->defaultValue(null)
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('login_check')->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('name')
                                                    ->defaultValue(null)
                                                ->end()
                                                ->scalarNode('path')
                                                    ->defaultValue(null)
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('logout')->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('name')
                                                    ->defaultValue(null)
                                                ->end()
                                                ->scalarNode('path')
                                                    ->defaultValue(null)
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->scalarNode('success_redirection_route')
                                            ->defaultValue(null)
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('registration')->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('enabled')
                                            ->defaultValue(true)
                                        ->end()
                                        ->scalarNode('type')
                                            ->defaultValue('default')
                                            ->validate()
                                            ->ifNotInArray(['default', 'by_invitation'])
                                                ->thenInvalid('Invalid registration type "%s"')
                                            ->end()
                                        ->end()
                                        ->scalarNode('name')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('path')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('invitation_name')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('invitation_path')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('success_route_name')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('success_redirection_route')
                                            ->defaultValue(null)
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
