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

namespace BenGorUser\UserBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Routes loader base class.
 *
 * Service that loads dynamically routes via PHP.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
abstract class RoutesLoader implements LoaderInterface
{
    /**
     * Array which contains the
     * routes configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Boolean that checks if the
     * routes are already loaded or not.
     *
     * @var bool
     */
    protected $loaded;

    /**
     * Collection of routes.
     *
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Constructor.
     *
     * @param array $config Array which contains the routes configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->loaded = false;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }
        if (empty($this->config)) {
            return;
        }

        $this->routes = new RouteCollection();
        foreach ($this->config as $user => $config) {
            if (false === array_key_exists('enabled', $config)) {
                throw new \RuntimeException('"enabled" key must be defined');
            }
            if (false === $config['enabled']) {
                continue;
            }
            if (true === array_key_exists('type', $config)) {
                $config['type'] = $this->sanitize($config['type']);
            }
            $this->register($user, $config);
        }
        $this->loaded = true;

        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
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
        return $specificationName;
    }

    /**
     * Registers a new route inside route
     * collection with the given params.
     *
     * @param string $user   The user name
     * @param array  $config The user configuration array
     */
    abstract protected function register($user, array $config);
}
