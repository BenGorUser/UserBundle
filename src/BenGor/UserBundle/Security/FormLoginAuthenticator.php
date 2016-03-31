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

namespace BenGor\UserBundle\Security;

use BenGor\User\Application\Service\LogInUserRequest;
use BenGor\User\Application\Service\LogInUserService;
use BenGor\User\Domain\Model\Exception\UserPasswordInvalidException;
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserFactory;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
use BenGor\User\Domain\Model\UserRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * Form login authenticator class.
 *
 * It centralizes all the login process
 * logic around the Symfony security component.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class FormLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * The user factory.
     *
     * @var UserFactory
     */
    private $factory;

    /**
     * The Symfony router component.
     *
     * @var Router
     */
    private $router;

    /**
     * The login route name.
     *
     * @var string
     */
    private $loginRoute;

    /**
     * The login_check route name.
     *
     * @var string
     */
    private $loginCheckRoute;

    /**
     * The success redirection route name.
     *
     * @var string
     */
    private $successRedirectionRoute;

    /**
     * The log in user service.
     *
     * @var LogInUserService
     */
    private $service;

    /**
     * Constructor.
     *
     * @param Router           $aRouter        The Symfony router component
     * @param LogInUserService $aService       The log in user service
     * @param UserFactory      $aFactory       The user factory
     * @param array            $securityRoutes The routes related with security (login, login_check and logout)
     */
    public function __construct(Router $aRouter, LogInUserService $aService, UserFactory $aFactory, $securityRoutes)
    {
        $this->factory = $aFactory;
        $this->router = $aRouter;
        $this->service = $aService;

        $this->loginRoute = $securityRoutes['login'];
        $this->loginCheckRoute = $securityRoutes['login_check'];
        $this->successRedirectionRoute = $securityRoutes['success_redirection_route'];
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        if ($this->loginCheckRoute !== $request->attributes->get('_route')) {
            return;
        }
        $email = $request->request->get('_email');
        $request->getSession()->set(Security::LAST_USERNAME, $email);
        $password = $request->request->get('_password');

        return new LogInUserRequest($email, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $response = $this->service->execute($credentials);
        } catch (\Exception $exception) {
            if ($exception instanceof UserPasswordInvalidException) {
                return $this->factory->register(
                    new UserId(),
                    new UserEmail('bengoruser@bengoruser.com'),
                    UserPassword::fromEncoded('0', 'this-is-trade-off'),
                    [new UserRole('ROLE_USER')]
                );
            }

            return;
        }

        return $response->user();
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return '0' !== $user->getPassword();
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl()
    {
        return $this->router->generate($this->loginRoute);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate($this->successRedirectionRoute);
    }
}
