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
     * Array which contains the routes configuration.
     *
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param array $config Array which contains the routes configuration
     */
    public function __construct(array $config)
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

        $routes = new RouteCollection();
        foreach ($this->config as $userConfig) {
            $securityRouteConfig = $userConfig['routes']['security'];
            $securityUseCaseConfig = $userConfig['use_cases']['security'];
            if (false === $securityUseCaseConfig['enabled']) {
                continue;
            }

            $routes->add($securityRouteConfig['login']['name'], new Route(
                $securityRouteConfig['login']['path'],
                [
                    '_controller'  => 'BenGorUserBundle:Security:login',
                    'successRoute' => $securityRouteConfig['success_redirection_route'],
                ],
                [],
                [],
                '',
                [],
                ['GET', 'POST']
            ));
            $routes->add($securityRouteConfig['login_check']['name'], new Route(
                $securityRouteConfig['login_check']['path'],
                [
                    '_controller' => 'BenGorUserBundle:Security:loginCheck',
                ],
                [],
                [],
                '',
                [],
                ['POST']
            ));
            $routes->add($securityRouteConfig['logout']['name'], new Route(
                $securityRouteConfig['logout']['path'],
                [
                    '_controller' => 'BenGorUserBundle:Security:logout',
                ],
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
        return 'bengor_user_security' === $type;
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
