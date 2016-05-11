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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Routing;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Base routes loader builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
abstract class RoutesLoaderBuilder
{
    /**
     * Configuration array.
     *
     * @var array
     */
    protected $configuration;

    /**
     * The container builder.
     *
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerBuilder $container     The container builder
     * @param array            $configuration The configuration tree
     */
    public function __construct(ContainerBuilder $container, array $configuration = [])
    {
        $this->configuration = $this->sanitize($configuration);
        $this->container = $container;
    }

    /**
     * Entry point of routes loader builder to
     * inject routes inside route loader.
     *
     * @return ContainerBuilder
     */
    public function build()
    {
        if (!$this->container->hasDefinition($this->definitionName())) {
            return;
        }
        $this->container->getDefinition(
            $this->definitionName()
        )->replaceArgument(0, array_unique($this->configuration, SORT_REGULAR));

        return $this->container;
    }

    /**
     * Gets the configuration after sanitize process.
     *
     * @return array
     */
    public function configuration()
    {
        return $this->configuration;
    }

    /**
     * Sanitizes and validates the given configuration tree.
     *
     * @param array $configuration The configuration tree
     *
     * @return array
     */
    protected function sanitize(array $configuration)
    {
        foreach ($configuration as $key => $config) {
            if (null === $config['name']) {
                $configuration[$key]['name'] = $this->defaultRouteName($key);
            }
            if (null === $config['path']) {
                $configuration[$key]['path'] = $this->defaultRoutePath($key);
            }
        }

        return $configuration;
    }

    /**
     * Gets the route loader's default route name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    protected function defaultRouteName($user)
    {
    }

    /**
     * Gets the route loader's default route path.
     *
     * @param string $user The user name
     *
     * @return string
     */
    protected function defaultRoutePath($user)
    {
    }

    /**
     * Gets the service definition name.
     *
     * @return string
     */
    abstract protected function definitionName();
}
