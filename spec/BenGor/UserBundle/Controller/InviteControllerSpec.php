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
use BenGor\UserBundle\Controller\InviteController;
use BenGor\UserBundle\Form\Type\InviteType;
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

/**
 * Spec file of InviteController class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class InviteControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InviteController::class);
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
        $formFactory->create(InviteType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/invite/invite.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->inviteAction($request, 'user')->shouldReturn($response);
    }

    function it_invites_action(
        SignUpUserService $service,
        SignUpUserRequest $signUpUserRequest,
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
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(InviteType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.invite_user')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($signUpUserRequest);

        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/invite/invite.html.twig', [
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
        $formFactory->create(InviteType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/invite/invite.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->inviteAction($request, 'user')->shouldReturn($response);
    }
}
