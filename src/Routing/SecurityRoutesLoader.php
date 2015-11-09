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

namespace BenGor\UserBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Security routes loader class.
 *
 * Service that loads dynamically routes of security.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityRoutesLoader implements LoaderInterface
{
    /**
     * Boolean that checks if the routes are already loaded or not.
     *
     * @var bool
     */
    private $loaded;

    /**
     * Array which contains the patterns.
     *
     * @var array
     */
    private $patterns;

    /**
     * Constructor.
     *
     * @param array $patterns Array which contains the patterns
     */
    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
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

        $routes = new RouteCollection();
        foreach ($this->patterns as $name => $pattern) {
            $routes->add('bengor_user' . $name . '_security_login', new Route(
                '/' . $pattern . '/login',
                ['_controller' => 'BenGorUserBundle:Security:login'],
                [],
                [],
                '',
                [],
                ['GET', 'POST']
            ));
            $routes->add('bengor_user' . $name . '_security_login_check', new Route(
                '/' . $pattern . '/login_check',
                ['_controller' => 'BenGorUserBundle:Security:loginCheck'],
                [],
                [],
                '',
                [],
                ['POST']
            ));
            $routes->add('bengor_user' . $name . '_security_logout', new Route(
                '/' . $pattern . '/logout',
                ['_controller' => 'BenGorUserBundle:Security:logout'],
                [],
                [],
                '',
                [],
                ['GET']
            ));
        }
        $this->loaded = true;

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user' === $type;
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
}
