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
use BenGor\User\Domain\Model\UserEmail;
use BenGor\User\Domain\Model\UserGuest;
use BenGor\User\Domain\Model\UserGuestRepository;
use BenGor\User\Domain\Model\UserToken;
use BenGor\User\Infrastructure\Domain\Model\UserFactory;
use BenGor\User\Infrastructure\Domain\Model\UserGuestFactory;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserGuestRepository;
use BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use BenGor\User\Infrastructure\Security\Test\DummyUserPasswordEncoder;
use BenGor\UserBundle\Controller\InvitationController;
use BenGor\UserBundle\Form\Type\InvitationType;
use BenGor\UserBundle\Form\Type\RegistrationByInvitationType;
use BenGor\UserBundle\Model\User;
use Ddd\Application\Service\TransactionalApplicationService;
use Ddd\Infrastructure\Application\Service\DummySession;
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

/**
 * Spec file of invitation controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class InvitationControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InvitationController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_renders_invite_action(
        Request $request,
        ContainerInterface $container,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(InvitationType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/registration/invite.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->inviteAction($request, 'user')->shouldReturn($response);
    }

    function it_invites_action(
        Request $request,
        ContainerInterface $container,
        Session $session,
        FlashBagInterface $flashBag,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        TwigEngine $templating,
        Response $response,
        FormView $formView
    ) {
        $service = new TransactionalApplicationService(
            new InviteUserService(
                new InMemoryUserRepository(),
                new InMemoryUserGuestRepository(),
                new UserGuestFactory(UserGuest::class)
            ), new DummySession()
        );
        $serviceRequest = new InviteUserRequest('bengor@user.com');

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(InvitationType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.invite_user')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($serviceRequest);

        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/registration/invite.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->inviteAction($request, 'user');
    }

    function it_does_not_invite_action(
        Request $request,
        ContainerInterface $container,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(InvitationType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/registration/invite.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->inviteAction($request, 'user')->shouldReturn($response);
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

        $this->shouldThrow(NotFoundHttpException::class)->duringRegisterByInvitationAction(
            $request, $invitationToken, 'user', 'main', 'bengor_user_user_homepage'
        );
    }

    function it_renders_register_by_invitation_action(
        Request $request,
        ContainerInterface $container,
        UserGuestRepository $userGuestRepository,
        UserGuest $userGuest,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $invitationToken = new UserToken('invitation-token');
        $email = new UserEmail('bengor@user.com');
        $container->get('bengor_user.user_guest_repository')
            ->shouldBeCalled()->willReturn($userGuestRepository);
        $userGuestRepository->userGuestOfInvitationToken($invitationToken)
            ->shouldBeCalled()->willReturn($userGuest);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RegistrationByInvitationType::class, null, [
            'roles'            => ['ROLE_USER'],
            'invitation_token' => $invitationToken,
        ])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $userGuest->email()->shouldBeCalled()->willReturn($email);
        $templating->renderResponse('@BenGorUser/registration/register_by_invitation.html.twig', [
            'email' => 'bengor@user.com',
            'form'  => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->registerByInvitationAction(
            $request, $invitationToken, 'user', 'main', 'bengor_user_user_homepage'
        )->shouldReturn($response);
    }

    function it_registers_by_invitation_action(
        Request $request,
        ContainerInterface $container,
        UserGuestRepository $userGuestRepository,
        UserGuest $userGuest,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        Session $session,
        FlashBagInterface $flashBag
    ) {
        $passwordEncoder = new DummyUserPasswordEncoder('dummy');
        $service = new TransactionalApplicationService(
            new SignUpUserByInvitationService(
                new InMemoryUserRepository(),
                new InMemoryUserGuestRepository(),
                $passwordEncoder,
                new UserFactory(User::class)
            ), new DummySession()
        );
        $serviceRequest = new SignUpUserByInvitationRequest('invitation-token', 123456, ['ROLE_USER']);
        $invitationToken = new UserToken('invitation-token');
        $email = new UserEmail('bengor@user.com');

        $container->get('bengor_user.user_guest_repository')
            ->shouldBeCalled()->willReturn($userGuestRepository);
        $userGuestRepository->userGuestOfInvitationToken($invitationToken)
            ->shouldBeCalled()->willReturn($userGuest);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RegistrationByInvitationType::class, null, [
            'roles'            => ['ROLE_USER'],
            'invitation_token' => $invitationToken,
        ])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.sign_up_user')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($serviceRequest);

        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $userGuest->email()->shouldBeCalled()->willReturn($email);
        $templating->renderResponse('@BenGorUser/registration/register_by_invitation.html.twig', [
            'email' => 'bengor@user.com',
            'form'  => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->registerByInvitationAction(
            $request, $invitationToken, 'user', 'main', 'bengor_user_user_homepage'
        )->shouldReturn($response);
    }

    function it_does_not_register_by_invitation_action(
        Request $request,
        ContainerInterface $container,
        UserGuestRepository $userGuestRepository,
        UserGuest $userGuest,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $invitationToken = new UserToken('invitation-token');
        $email = new UserEmail('bengor@user.com');
        $container->get('bengor_user.user_guest_repository')
            ->shouldBeCalled()->willReturn($userGuestRepository);
        $userGuestRepository->userGuestOfInvitationToken($invitationToken)
            ->shouldBeCalled()->willReturn($userGuest);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RegistrationByInvitationType::class, null, [
            'roles'            => ['ROLE_USER'],
            'invitation_token' => $invitationToken,
        ])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $userGuest->email()->shouldBeCalled()->willReturn($email);
        $templating->renderResponse('@BenGorUser/registration/register_by_invitation.html.twig', [
            'email' => 'bengor@user.com',
            'form'  => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->registerByInvitationAction(
            $request, $invitationToken, 'user', 'main', 'bengor_user_user_homepage'
        )->shouldReturn($response);
    }
}
