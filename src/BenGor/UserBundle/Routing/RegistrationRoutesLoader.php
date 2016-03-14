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
        foreach ($this->config as $userClass => $userConfig) {
            $registrationRouteConfig = $userConfig['routes']['registration'];
            if (false === $registrationRouteConfig['enabled']) {
                continue;
            }

            $registerAction = 'register';
            if ('by_invitation' === $registrationRouteConfig['type']) {
                $routes->add(
                    $registrationRouteConfig['invitation_name'],
                    new Route(
                        $registrationRouteConfig['invitation_path'],
                        [
                            '_controller' => 'BenGorUserBundle:Registration:invite',
                            'userClass'   => $userClass,
                        ],
                        [],
                        [],
                        '',
                        [],
                        ['GET', 'POST']
                    )
                );
                $registerAction = 'registerByInvitation';
            }

            $routes->add($registrationRouteConfig['name'], new Route(
                $registrationRouteConfig['path'],
                [
                    '_controller'  => 'BenGorUserBundle:Registration:' . $registerAction,
                    'userClass'    => $userClass,
                    'firewall'     => $userConfig['firewall'],
                    'successRoute' => $registrationRouteConfig['success_redirection_route'],
                ],
                [],
                [],
                '',
                [],
                ['GET', 'POST']
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
