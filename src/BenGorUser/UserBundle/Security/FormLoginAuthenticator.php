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
use BenGorUser\User\Domain\Model\UserUrlGenerator;
use BenGorUser\UserBundle\CommandBus\UserCommandBus;
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
     * The command bus.
     *
     * @var UserCommandBus
     */
    private $commandBus;

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
     * @param UserUrlGenerator $aUserUrlGenerator The user URL generator
     * @param UserCommandBus   $aCommandBus       The command bus
     * @param array            $routes            The routes related with security (login, login_check and logout)
     */
    public function __construct(UserUrlGenerator $aUserUrlGenerator, UserCommandBus $aCommandBus, array $routes)
    {
        $this->urlGenerator = $aUserUrlGenerator;
        $this->commandBus = $aCommandBus;

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
     * User DTO instantiation is needed to continue the correct
     * Guard flow. Then, the process will break in "checkCredentials"
     * method throwing the correct error message.
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $this->commandBus->handle($credentials);
        } catch (UserPasswordInvalidException $exception) {
            return new User('bengor@user.com', '0', ['ROLE_USER']);
        } catch (\Exception $exception) {
            return;
        }

        return $userProvider->loadUserByUsername($credentials->email());
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
                'user_email' => $token->getUser()->getUsername(),
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
