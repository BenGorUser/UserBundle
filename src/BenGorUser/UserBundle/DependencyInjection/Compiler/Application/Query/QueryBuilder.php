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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application\Query;

use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\ApplicationBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Base query builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
abstract class QueryBuilder implements ApplicationBuilder
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
        $this->container = $container;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function build($user)
    {
        $this->register($user);

        $this->container->setAlias(
            $this->aliasDefinitionName($user),
            $this->definitionName($user)
        );

        return $this->container;
    }

    /**
     * Gets the query definition name.
     *
     * @param string $user The user name
     *
     * @return string
     */
    abstract protected function definitionName($user);

    /**
     * Gets the query definition name alias.
     *
     * @param string $user The user name
     *
     * @return string
     */
    abstract protected function aliasDefinitionName($user);

    /**
     * Registers the query into container.
     *
     * @param string $user The user name
     */
    abstract protected function register($user);
}
