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
 * Registration routes loader class.
 *
 * Service that loads dynamically routes of registration.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegistrationRoutesLoader implements LoaderInterface
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
        foreach ($this->patterns as $name => $route) {
            $routes->add(
                'bengor_user_' . $name . '_registration',
                new Route(
                    $route['register_path'],
                    [
                        '_controller' => 'BenGorUserBundle:Registration:' . $route['action'],
                        'userClass'   => $name,
                        'firewall'    => $route['firewall'],
                        'pattern'     => $route['pattern'],
                    ],
                    [],
                    [],
                    '',
                    [],
                    ['GET', 'POST']
                )
            );
            $routes->add(
                'bengor_user_' . $name . '_invite',
                new Route(
                    $route['invite_path'],
                    [
                        '_controller' => 'BenGorUserBundle:Registration:invite',
                        'userClass'   => $name,
                    ],
                    [],
                    [],
                    '',
                    [],
                    ['GET', 'POST']
                )
            );
        }
        $this->loaded = true;

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'bengor_user_registration' === $type;
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
