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

use BenGorUser\UserBundle\Security\JWTAuthenticator;
use BenGorUser\UserBundle\Security\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Spec file of JWTAuthenticator class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class JWTAuthenticatorSpec extends ObjectBehavior
{
    function let(JWTEncoderInterface $jwtEncoder)
    {
        $this->beConstructedWith($jwtEncoder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JWTAuthenticator::class);
    }

    function it_extends_abstract_guard_authenticator()
    {
        $this->shouldHaveType(AbstractGuardAuthenticator::class);
    }

    function it_gets_credentials(Request $request, ParameterBagInterface $headersBag)
    {
        $headersBag->has('Authorization')->shouldBeCalled()->willReturn(true);
        $headersBag->get('Authorization')->shouldBeCalled()->willReturn('Bearer bearer-token');
        $request->headers = $headersBag;

        $this->getCredentials($request)->shouldReturn('bearer-token');
    }

    function it_gets_user(JWTEncoderInterface $jwtEncoder, UserProviderInterface $userProvider, User $user)
    {
        $user->confirmationToken = null;
        $user->invitationToken = null;
        $jwtEncoder->decode('bearer-token')->shouldBeCalled()->willReturn(['email' => 'bengor@user.com']);
        $userProvider->loadUserByUsername('bengor@user.com')->shouldBeCalled()->willReturn($user);

        $this->getUser('bearer-token', $userProvider)->shouldReturn($user);
    }

    function it_does_not_get_user_because_token_is_invalid(
        JWTEncoderInterface $jwtEncoder,
        UserProviderInterface $userProvider
    ) {
        $jwtEncoder->decode('invalid-token')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow(
            new CustomUserMessageAuthenticationException('The given token is invalid')
        )->duringGetUser('invalid-token', $userProvider);
    }

    function it_does_not_get_user_because_user_is_inactive(
        JWTEncoderInterface $jwtEncoder,
        UserProviderInterface $userProvider,
        User $user
    ) {
        $jwtEncoder->decode('bearer-token')->shouldBeCalled()->willReturn(['email' => 'bengor@user.com']);
        $userProvider->loadUserByUsername('bengor@user.com')->shouldBeCalled()->willReturn($user);
        $user->confirmationToken = 'confirmation-token';
        $user->invitationToken = null;

        $this->shouldThrow(
            new CustomUserMessageAuthenticationException('The user does not exist')
        )->duringGetUser('bearer-token', $userProvider);
    }

    function it_checks_credentials(User $user)
    {
        $this->checkCredentials('bearer-token', $user)->shouldReturn(true);
    }

    function it_on_authenticate_failure(Request $request, AuthenticationException $exception)
    {
        $this->onAuthenticationFailure($request, $exception);
    }

    function it_on_authenticate_success(Request $request, TokenInterface $token)
    {
        $this->onAuthenticationSuccess($request, $token, 'provider-key');
    }

    function it_supports_remember_me()
    {
        $this->supportsRememberMe()->shouldReturn(false);
    }

    function it_starts(Request $request, AuthenticationException $exception)
    {
        $this->start($request, $exception)->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
