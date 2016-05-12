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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Service;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Base service builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
abstract class ServiceBuilder
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

        if (true === array_key_exists('type', $configuration)) {
            $this->specification = $this->sanitize($configuration['type']);
        }
    }

    /**
     * Entry point of service builder to create
     * the application service inside Symfony DIC.
     *
     * @param string $user The user name
     *
     * @return ContainerBuilder
     */
    public function build($user)
    {
        $enabled = array_key_exists('enabled', $this->configuration)
            ? $this->configuration['enabled']
            : true;

        if (false === $enabled) {
            return;
        }
        $this->register($user);

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

    /**
     * Gets the service definition name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    abstract protected function definitionName($user);

    /**
     * Gets the service definition name alias.
     *
     * @param string $user The user name
     *
     * @return string
     */
    abstract protected function aliasDefinitionName($user);

    /**
     * Registers the service into container.
     *
     * @param string $user The user name
     */
    abstract protected function register($user);
}
