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

use BenGor\User\Application\Service\SignUp\SignUpUserRequest;
use BenGor\User\Application\Service\SignUp\SignUpUserService;
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserGuest;
use BenGor\User\Domain\Model\UserGuestRepository;
use BenGor\User\Domain\Model\UserToken;
use BenGor\UserBundle\Controller\SignUpController;
use BenGor\UserBundle\Form\Type\SignUpByInvitationType;
use BenGor\UserBundle\Form\Type\SignUpType;
use BenGor\UserBundle\Model\User;
use BenGor\UserBundle\Security\FormLoginAuthenticator;
use Ddd\Application\Service\TransactionalApplicationService;
use PhpSpec\ObjectBehavior;
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
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Translation\Translator;

/**
 * Spec file of SignUpController class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SignUpController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_renders_default_action(
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
        $formFactory->create(SignUpType::class, null, ['roles' => ['ROLE_USER']])
            ->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/sign_up/default.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->defaultAction($request, 'user', 'main', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_default_action(
        TransactionalApplicationService $service,
        SignUpUserRequest $signUpUserRequest,
        Request $request,
        ContainerInterface $container,
        GuardAuthenticatorHandler $handler,
        FormLoginAuthenticator $formLoginAuthenticator,
        Session $session,
        Response $response,
        FlashBagInterface $flashBag,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        User $user,
        Translator $translator
    ) {
        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(SignUpType::class, null, ['roles' => ['ROLE_USER']])
            ->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.sign_up_user')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($signUpUserRequest);

        $service->execute($signUpUserRequest)->shouldBeCalled()->willReturn($user);

        $container->get('translator')->shouldBeCalled()->willReturn($translator);
        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->get('security.authentication.guard_handler')->shouldBeCalled()->willReturn($handler);
        $container->get('bengor_user.form_login_user_authenticator')
            ->shouldBeCalled()->willReturn($formLoginAuthenticator);
        $handler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $formLoginAuthenticator,
            'main'
        )->shouldBeCalled()->willReturn($response);

        $this->defaultAction($request, 'user', 'main')->shouldReturn($response);
    }

    function it_does_not_sign_up_default_action(
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
        $formFactory->create(SignUpType::class, null, ['roles' => ['ROLE_USER']])
            ->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/sign_up/default.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->defaultAction($request, 'user', 'main', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_renders_by_invitation_action(
        UserGuestRepository $userGuestRepository,
        UserGuest $userGuest,
        Request $request,
        ContainerInterface $container,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('bengor_user.user_guest_repository')->shouldBeCalled()->willReturn($userGuestRepository);
        $userGuestRepository->userGuestOfInvitationToken(new UserToken('invitation-token'))
            ->shouldBeCalled()->willReturn($userGuest);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(SignUpByInvitationType::class, null, [
            'roles'            => ['ROLE_USER'],
            'invitation_token' => 'invitation-token',
        ])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $userGuest->email()->shouldBeCalled()->willReturn(new UserEmail('user@guest.com'));
        $templating->renderResponse('@BenGorUser/sign_up/by_invitation.html.twig', [
            'email' => 'user@guest.com',
            'form'  => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->byInvitationAction(
            $request, 'invitation-token', 'user', 'main', 'bengor_user_user_homepage'
        )->shouldReturn($response);
    }

    function it_by_invitation_action(
        SignUpUserService $service,
        SignUpUserRequest $signUpUserRequest,
        UserGuestRepository $userGuestRepository,
        UserGuest $userGuest,
        Request $request,
        ContainerInterface $container,
        Session $session,
        Translator $translator,
        FlashBagInterface $flashBag,
        GuardAuthenticatorHandler $handler,
        Response $response,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        User $user,
        FormLoginAuthenticator $formLoginAuthenticator
    ) {
        $container->get('bengor_user.user_guest_repository')->shouldBeCalled()->willReturn($userGuestRepository);
        $userGuestRepository->userGuestOfInvitationToken(new UserToken('invitation-token'))
            ->shouldBeCalled()->willReturn($userGuest);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(SignUpByInvitationType::class, null, [
            'roles'            => ['ROLE_USER'],
            'invitation_token' => 'invitation-token',
        ])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.sign_up_user')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($signUpUserRequest);

        $service->execute($signUpUserRequest)->shouldBeCalled()->willReturn($user);

        $container->get('translator')->shouldBeCalled()->willReturn($translator);
        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->get('security.authentication.guard_handler')->shouldBeCalled()->willReturn($handler);
        $container->get('bengor_user.form_login_user_authenticator')
            ->shouldBeCalled()->willReturn($formLoginAuthenticator);
        $handler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $formLoginAuthenticator,
            'main'
        )->shouldBeCalled()->willReturn($response);

        $this->byInvitationAction($request, 'invitation-token', 'user', 'main')->shouldReturn($response);
    }

    function it_does_not_sign_up_by_invitation_action(
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormView $formView,
        TwigEngine $templating,
        Response $response,
        UserGuestRepository $userGuestRepository,
        UserGuest $userGuest
    ) {
        $container->get('bengor_user.user_guest_repository')->shouldBeCalled()->willReturn($userGuestRepository);
        $userGuestRepository->userGuestOfInvitationToken(new UserToken('invitation-token'))
            ->shouldBeCalled()->willReturn($userGuest);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(SignUpByInvitationType::class, null, [
            'roles'            => ['ROLE_USER'],
            'invitation_token' => 'invitation-token',
        ])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $userGuest->email()->shouldBeCalled()->willReturn(new UserEmail('user@guest.com'));
        $templating->renderResponse('@BenGorUser/sign_up/by_invitation.html.twig', [
            'email' => 'user@guest.com',
            'form'  => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->byInvitationAction(
            $request, 'invitation-token', 'user', 'main', 'bengor_user_user_homepage'
        )->shouldReturn($response);
    }

    function it_does_not_render_because_invitation_token_does_not_exist(
        Request $request,
        ContainerInterface $container,
        UserGuestRepository $userGuestRepository
    ) {
        $invitationToken = new UserToken('invitation-token');
        $container->get('bengor_user.user_guest_repository')
            ->shouldBeCalled()->willReturn($userGuestRepository);
        $userGuestRepository->userGuestOfInvitationToken($invitationToken)
            ->shouldBeCalled()->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->duringByInvitationAction(
            $request, $invitationToken, 'user', 'main', 'bengor_user_user_homepage'
        );
    }
}