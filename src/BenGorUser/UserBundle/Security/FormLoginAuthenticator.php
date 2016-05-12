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

namespace BenGorUser\UserBundle\Security;

use BenGorUser\User\Application\Service\LogIn\LogInUserCommand;
use BenGorUser\User\Domain\Model\Exception\UserPasswordInvalidException;
use BenGorUser\User\Domain\Model\UserEmail;
use BenGorUser\User\Domain\Model\UserId;
use BenGorUser\User\Domain\Model\UserPassword;
use BenGorUser\User\Domain\Model\UserRole;
use BenGorUser\User\Domain\Model\UserUrlGenerator;
use BenGorUser\UserBundle\Model\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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
     * The login_check route name.
     *
     * @var string
     */
    private $loginCheckRoute;

    /**
     * The login route name.
     *
     * @var string
     */
    private $loginRoute;

    /**
     * The The authenticator service.
     *
     * @var AuthenticatorService
     */
    private $service;

    /**
     * The success redirection route name.
     *
     * @var string
     */
    private $successRedirectionRoute;

    /**
     * The user URL generator.
     *
     * @var UserUrlGenerator
     */
    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param UserUrlGenerator     $aUserUrlGenerator The user URL generator
     * @param AuthenticatorService $aService          The authenticator service
     * @param array                $routes            The routes related with security (login, login_check and logout)
     */
    public function __construct(UserUrlGenerator $aUserUrlGenerator, AuthenticatorService $aService, array $routes)
    {
        $this->urlGenerator = $aUserUrlGenerator;
        $this->service = $aService;

        if (false === isset($routes['login'], $routes['login_check'], $routes['success_redirection_route'])) {
            throw new \InvalidArgumentException(
                '"routes" array should have "login", "login_check" and "success_redirection_route" keys'
            );
        }
        $this->loginRoute = $routes['login'];
        $this->loginCheckRoute = $routes['login_check'];
        $this->successRedirectionRoute = $routes['success_redirection_route'];
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

        return new LogInUserCommand($email, $password);
    }

    /**
     * {@inheritdoc}
     *
     * User instantiation inside catch is needed to continue the
     * correct Guard flow. Then, the process will break in
     * "checkCredentials" method throwing the correct error message.
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $response = $this->service->execute($credentials);
        } catch (\Exception $exception) {
            if ($exception instanceof UserPasswordInvalidException) {
                return new User(
                    new UserId(),
                    new UserEmail('bengor@user.com'),
                    UserPassword::fromEncoded('0', 'the-salt'),
                    [new UserRole('ROLE_USER')]
                );
            }

            return;
        }

        return $response;
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
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => $exception->getMessageKey()], 403);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'user_id'    => $token->getUser()->id()->id(),
                'user_email' => $token->getUser()->email()->email(),
            ]);
        }

        return parent::onAuthenticationSuccess($request, $token, $providerKey);
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate($this->loginRoute, [], UserUrlGenerator::ABSOLUTE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->urlGenerator->generate($this->successRedirectionRoute, [], UserUrlGenerator::ABSOLUTE_PATH);
    }
}
