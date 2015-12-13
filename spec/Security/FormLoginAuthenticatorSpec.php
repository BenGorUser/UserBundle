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

use BenGor\User\Application\Service\LogInUserRequest;
use BenGor\User\Application\Service\LogInUserService;
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserId;
use BenGor\User\Domain\Model\UserPassword;
use BenGor\User\Domain\Model\UserRole;
use BenGor\User\Infrastructure\Domain\Model\UserFactory;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use BenGor\UserBundle\Model\User;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Spec file of form login authenticator class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class FormLoginAuthenticatorSpec extends ObjectBehavior
{
    private $service;
    private $user;

    function let(Router $router)
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

        $this->service = new LogInUserService(
            $inMemoryUserRepository,
            new DummyUserPasswordEncoder('111111')
        );

        $this->beConstructedWith(
            $router, $this->service, new UserFactory('BenGor\User\Bundle\Model\User'), ''
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('BenGor\UserBundle\Security\FormLoginAuthenticator');
    }

    function it_extends_abstract_form_login_authenticator()
    {
        $this->shouldHaveType('Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator');
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
            'bengor_user_security_login_check'
        );
        $request->request = $parameterBag;
        $request->attributes = $attributesBag;

        $request->getSession()->shouldBeCalled()->willReturn($session);
        $session->set(Security::LAST_USERNAME, 'test@test.com')->shouldBeCalled();

        $this->getCredentials($request)->shouldReturnAnInstanceOf('BenGor\User\Application\Service\LogInUserRequest');
    }

    function it_gets_user(UserProviderInterface $userProvider)
    {
        $credentials = new LogInUserRequest('test@test.com', '111111');
        $this->service->execute($credentials);

        $this->getUser($credentials, $userProvider)->shouldReturnAnInstanceOf('Symfony\Component\Security\Core\User\UserInterface');
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
