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

namespace spec\BenGor\UserBundle\Controller;

use BenGor\User\Application\Service\InviteUserRequest;
use BenGor\User\Application\Service\InviteUserService;
use BenGor\User\Application\Service\SignUpUserByInvitationRequest;
use BenGor\User\Application\Service\SignUpUserByInvitationService;
use BenGor\User\Application\Service\SignUpUserRequest;
use BenGor\User\Application\Service\SignUpUserService;
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserGuest;
use BenGor\User\Domain\Model\UserGuestRepository;
use BenGor\User\Domain\Model\UserToken;
use BenGor\User\Infrastructure\Domain\Model\UserFactory;
use BenGor\User\Infrastructure\Domain\Model\UserGuestFactory;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use BenGor\User\Infrastructure\Security\Test\DummyUserPasswordEncoder;
use BenGor\UserBundle\Controller\RegistrationController;
use BenGor\UserBundle\Form\Type\RegistrationType;
use BenGor\UserBundle\Model\User;
use BenGor\UserBundle\Security\FormLoginAuthenticator;
use Ddd\Application\Service\TransactionalApplicationService;
use Ddd\Infrastructure\Application\Service\DummySession;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Spec file of registration controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegistrationControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RegistrationController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_renders_register_action(
        Request $request,
        ContainerInterface $container,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RegistrationType::class, null, ['roles' => ['ROLE_USER']])
            ->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/registration/register.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->registerAction($request, 'user', 'main', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_registers_action(
        Request $request,
        ContainerInterface $container,
        GuardAuthenticatorHandler $handler,
        FormLoginAuthenticator $formLoginAuthenticator,
        Session $session,
        TwigEngine $templating,
        FlashBagInterface $flashBag,
        FormInterface $form,
        FormView $formView,
        FormFactoryInterface $formFactory
    ) {
        $passwordEncoder = new DummyUserPasswordEncoder('dummy');
        $service = new TransactionalApplicationService(
            new SignUpUserService(
                new InMemoryUserRepository(),
                $passwordEncoder,
                new UserFactory(User::class)
            ), new DummySession()
        );
        $serviceRequest = new SignUpUserRequest('bengor@user.com', 123456, ['ROLE_USER']);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RegistrationType::class, null, ['roles' => ['ROLE_USER']])
            ->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.sign_up_user')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($serviceRequest);

        $container->get('security.authentication.guard_handler')->shouldBeCalled()->willReturn($handler);
        $container->get('bengor_user.form_login_user_authenticator')
            ->shouldBeCalled()->willReturn($formLoginAuthenticator);
        $handler->authenticateUserAndHandleSuccess(
            Argument::type(User::class),
            $request,
            $formLoginAuthenticator,
            'main'
        )->shouldBeCalled();

        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);

        $this->registerAction($request, 'user', 'main', 'bengor_user_user_homepage');
    }

    function it_does_not_register_action(
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormView $formView,
        TwigEngine $templating,
        Response $response
    ) {
        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RegistrationType::class, null, ['roles' => ['ROLE_USER']])
            ->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/registration/register.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->registerAction($request, 'user', 'main', 'bengor_user_user_homepage')->shouldReturn($response);
    }
}
