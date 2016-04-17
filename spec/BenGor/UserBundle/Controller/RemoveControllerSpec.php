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

use BenGor\User\Application\Service\Remove\RemoveUserRequest;
use BenGor\User\Application\Service\Remove\RemoveUserService;
use BenGor\UserBundle\Controller\RemoveController;
use BenGor\UserBundle\Form\Type\RemoveType;
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
use Symfony\Component\Translation\Translator;

/**
 * Spec file of RemoveController class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RemoveControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RemoveController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_renders_remove_action(
        Request $request,
        Response $response,
        ContainerInterface $container,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormView $formView,
        TwigEngine $templating
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RemoveType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/remove/remove.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->removeAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_removes_action(
        RemoveUserService $service,
        RemoveUserRequest $signUpUserRequest,
        Request $request,
        ContainerInterface $container,
        Session $session,
        FlashBagInterface $flashBag,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        Translator $translator
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RemoveType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.remove_user')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($signUpUserRequest);

        $container->get('translator')->shouldBeCalled()->willReturn($translator);
        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/remove/remove.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->removeAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_does_not_remove_action(
        Request $request,
        ContainerInterface $container,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RemoveType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/remove/remove.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->removeAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }
}
