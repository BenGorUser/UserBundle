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

namespace spec\BenGorUser\UserBundle\Security;

use BenGorUser\User\Application\Command\LogIn\LogInUserCommand;
use BenGorUser\User\Domain\Model\UserUrlGenerator;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Security\FormLoginAuthenticator;
use BenGorUser\UserBundle\Security\User;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * Spec file of FormLoginAuthenticator class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class FormLoginAuthenticatorSpec extends ObjectBehavior
{
    function let(UserUrlGenerator $urlGenerator, UserCommandBus $commandBus)
    {
        $this->beConstructedWith($urlGenerator, $commandBus, [
            'login'                     => 'bengor_user_user_security_login',
            'login_check'               => 'bengor_user_user_security_login_check',
            'success_redirection_route' => 'bengor_user_user_security_homepage',
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FormLoginAuthenticator::class);
    }

    function it_extends_abstract_form_login_authenticator()
    {
        $this->shouldHaveType(AbstractFormLoginAuthenticator::class);
    }

    function it_throws_invalid_argument_exception_when_routes_are_not_provided(
        UserUrlGenerator $urlGenerator,
        UserCommandBus $commandBus
    ) {
        $this->beConstructedWith($urlGenerator, $commandBus, []);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_gets_credentials(
        Request $request,
        ParameterBagInterface $parameterBag,
        ParameterBagInterface $attributesBag,
        SessionInterface $session
    ) {
        $parameterBag->get('_email')->shouldBeCalled()->willReturn('test@test.com');
        $parameterBag->get('_password')->shouldBeCalled()->willReturn('111111');
        $attributesBag->get('_route')->shouldBeCalled()->willReturn(
            'bengor_user_user_security_login_check'
        );
        $request->request = $parameterBag;
        $request->attributes = $attributesBag;

        $request->getSession()->shouldBeCalled()->willReturn($session);
        $session->set(Security::LAST_USERNAME, 'test@test.com')->shouldBeCalled();

        $this->getCredentials($request)->shouldReturnAnInstanceOf(LogInUserCommand::class);
    }

    function it_on_authentication_failure_when_is_xml_http_request(Request $request, AuthenticationException $exception)
    {
        $request->isXmlHttpRequest()->shouldBeCalled()->willReturn(true);

        $this->onAuthenticationFailure($request, $exception)->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_on_authentication_success_when_is_xml_http_request(
        Request $request,
        TokenInterface $token,
        User $user
    ) {
        $request->isXmlHttpRequest()->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getUsername()->shouldBeCalled()->willReturn('bengor@user.com');

        $this->onAuthenticationSuccess($request, $token, 'main')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_gets_user(
        UserProviderInterface $userProvider,
        UserCommandBus $commandBus,
        LogInUserCommand $credentials,
        User $user
    ) {
        $commandBus->handle($credentials)->shouldBeCalled();
        $credentials->email()->shouldBeCalled()->willReturn('bengor@user.com');
        $userProvider->loadUserByUsername('bengor@user.com')->shouldBeCalled()->willReturn($user);

        $this->getUser($credentials, $userProvider)->shouldReturn($user);
    }

    function it_checks_credentials(LogInUserCommand $credentials, User $user)
    {
        $this->checkCredentials($credentials, $user)->shouldReturn(true);
    }
}
