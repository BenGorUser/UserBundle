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
use BenGor\User\Domain\Model\Exception\UserInvalidPasswordException;
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserFactory;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
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
     * The prefix of urls.
     *
     * The pattern of the configuration.
     *
     * @var string
     */
    private $pattern;

    /**
     * The Symfony router component.
     *
     * @var Router
     */
    private $router;

    /**
     * The log in user service.
     *
     * @var LogInUserService
     */
    private $service;

    /**
     * Constructor.
     *
     * @param Router           $aRouter  The Symfony router component
     * @param LogInUserService $aService The log in user service
     * @param UserFactory      $aFactory The user factory
     * @param string           $aPattern The pattern
     */
    public function __construct(Router $aRouter, LogInUserService $aService, UserFactory $aFactory, $aPattern)
    {
        $this->factory = $aFactory;
        $this->router = $aRouter;
        $this->service = $aService;

        $this->pattern = '';
        if ('' !== $aPattern) {
            $this->pattern = $aPattern . '_';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        if ('bengor_user_' . $this->pattern . 'security_login_check' !== $request->attributes->get('_route')) {
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
            if ($exception instanceof UserInvalidPasswordException) {
                return $this->factory->register(
                    new UserId(),
                    new UserEmail('bengoruser@bengoruser.com'),
                    UserPassword::fromEncoded('0', 'this-is-trade-off')
                );
            }

            return null;
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
        return $this->router->generate('bengor_user_' . $this->pattern . 'security_login');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate($this->pattern . 'homepage');
    }
}
