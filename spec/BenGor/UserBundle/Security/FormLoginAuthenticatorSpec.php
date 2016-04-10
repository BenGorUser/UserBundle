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
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
use BenGor\User\Domain\Model\UserRole;
use BenGor\User\Infrastructure\Domain\Model\UserFactory;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use BenGor\UserBundle\Model\User;
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
 * Spec file of form login authenticator class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class FormLoginAuthenticatorSpec extends ObjectBehavior
{
    private $user;

    function let(Router $router, LogInUserService $service)
    {
        $inMemoryUserRepository = new InMemoryUserRepository();
        $this->user = new User(
            new UserId(),
            new UserEmail('test@test.com'),
            UserPassword::fromEncoded('111111', 'dummy-salt'),
            [new UserRole('ROLE_USER')]
        );
        $this->user->enableAccount();
        $inMemoryUserRepository->persist($this->user);

        $this->beConstructedWith(
            $router, $service, new UserFactory(User::class), [
                'login'                     => 'bengor_user_user_security_login',
                'login_check'               => 'bengor_user_user_security_login_check',
                'success_redirection_route' => 'bengor_user_user_security_homepage',
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FormLoginAuthenticator::class);
    }

    function it_extends_abstract_form_login_authenticator()
    {
        $this->shouldHaveType(AbstractFormLoginAuthenticator::class);
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

    function it_gets_user(UserProviderInterface $userProvider, LogInUserService $service)
    {
        $credentials = new LogInUserRequest('test@test.com', '111111');
        $service->execute($credentials)->shouldBeCalled()->willReturn([
            'id' => 'user-id',
        ]);

        $this->getUser($credentials, $userProvider)->shouldReturn([
            'id' => 'user-id',
        ]);
    }

    function it_checks_credentials()
    {
        $credentials = new LogInUserRequest('test@test.com', '111111');
        $user = new User(
            new UserId(),
            new UserEmail('test@test.com'),
            UserPassword::fromEncoded('111111', 'dummy-salt'),
            [new UserRole('ROLE_USER')]
        );

        $this->checkCredentials($credentials, $user)->shouldReturn(true);
    }
}
