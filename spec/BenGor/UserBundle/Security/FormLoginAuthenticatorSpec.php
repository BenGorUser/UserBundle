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

namespace spec\BenGor\UserBundle\Security;

use BenGor\User\Application\Service\LogIn\LogInUserRequest;
use BenGor\User\Application\Service\LogIn\LogInUserService;
use BenGor\User\Domain\Model\UserUrlGenerator;
use BenGor\UserBundle\Model\User;
use BenGor\UserBundle\Security\AuthenticatorService;
use BenGor\UserBundle\Security\FormLoginAuthenticator;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
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
    function let(UserUrlGenerator $urlGenerator, AuthenticatorService $service)
    {
        $this->beConstructedWith($urlGenerator, $service, [
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
        LogInUserService $service
    ) {
        $this->beConstructedWith($urlGenerator, $service, []);

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

        $this->getCredentials($request)->shouldReturnAnInstanceOf(LogInUserRequest::class);
    }

    function it_gets_user(
        UserProviderInterface $userProvider,
        LogInUserService $service,
        LogInUserRequest $credentials,
        User $user
    ) {
        $service->execute($credentials)->shouldBeCalled()->willReturn($user);

        $this->getUser($credentials, $userProvider)->shouldReturn($user);
    }

    function it_checks_credentials(LogInUserRequest $credentials, User $user)
    {
        $this->checkCredentials($credentials, $user)->shouldReturn(true);
    }
}
