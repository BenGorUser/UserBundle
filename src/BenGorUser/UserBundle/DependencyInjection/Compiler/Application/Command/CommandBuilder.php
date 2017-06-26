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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Command;

use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\ApplicationBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Base command builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
abstract class CommandBuilder implements ApplicationBuilder
{
    /**
     * Flag that tells if the use case is enabled or not.
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Flag that tells if the api version of the use case is enabled or not.
     *
     * @var bool
     */
    protected $apiEnabled;

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
     * The persistence driver.
     *
     * @var string
     */
    protected $persistence;

    /**
     * The specification name.
     *
     * @var string
     */
    protected $specification;

    /**
     * The specification name.
     *
     * @var string
     */
    protected $apiSpecification;

    /**
     * Constructor.
     *
     * @param ContainerBuilder $container     The container builder
     * @param string           $persistence   The persistence driver
     * @param array            $configuration The configuration tree
     */
    public function __construct(ContainerBuilder $container, $persistence, array $configuration = [])
    {
        $this->container = $container;
        $this->persistence = $persistence;
        $this->configuration = $configuration;

        $this->enabled = array_key_exists('enabled', $this->configuration)
            ? $this->configuration['enabled']
            : true;
        $this->apiEnabled = array_key_exists('api_enabled', $this->configuration)
            ? $this->configuration['api_enabled']
            : false;

        if (true === array_key_exists('type', $configuration)) {
            $this->specification = $this->sanitize($configuration['type']);
        }
        if (true === array_key_exists('api_type', $configuration)) {
            $this->apiSpecification = $this->sanitize($configuration['api_type']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build($user)
    {
        if (true === $this->enabled) {
            $this->doBuild($user);
        }
        if (true === $this->apiEnabled) {
            $this->doBuild($user, true);
        }

        return $this->container;
    }

    /**
     * Wraps the service registration and the alias addtion.
     *
     * @param string $user  The user name
     * @param bool   $isApi Flag that tells if it is api version or not
     *
     * @return ContainerBuilder
     */
    protected function doBuild($user, $isApi = false)
    {
        $this->register($user, $isApi);

        $this->container->setAlias(
            $this->alias($user, $isApi),
            $this->definition($user, $isApi)
        );

        return $this->container;
    }

    /**
     * Sanitizes and validates the given specification name.
     *
     * @param string $specificationName The specification name
     *
     * @return string
     */
    protected function sanitize($specificationName)
    {
        return $specificationName . 'Specification';
    }

    protected function definition($user, $isApi = false)
    {
        $definition = $this->definitionName($user);
        $definition .= $isApi ? '_api' : '';

        return $definition;
    }

    protected function alias($user, $isApi = false)
    {
        $alias = $this->aliasDefinitionName($user);
        $alias .= $isApi ? '_api' : '';

        return $alias;
    }

    protected function commandHandlerTag($user, $isApi = false)
    {
        $apiPartName = $isApi ? '_api' : '';

        return 'bengor_user_' . $user . $apiPartName . '_command_bus_handler';
    }

    /**
     * Gets the command definition name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    abstract protected function definitionName($user);

    /**
     * Gets the command definition name alias.
     *
     * @param string $user The user name
     *
     * @return string
     */
    abstract protected function aliasDefinitionName($user);

    /**
     * Registers the command into container.
     *
     * @param string $user  The user name
     * @param bool   $isApi Flag that tells if it is api version or not
     */
    abstract protected function register($user, $isApi = false);
}
