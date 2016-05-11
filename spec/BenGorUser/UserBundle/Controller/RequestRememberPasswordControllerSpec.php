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

namespace spec\BenGorUser\UserBundle\Controller;

use BenGorUser\User\Application\Service\RequestRememberPassword\RequestRememberPasswordRequest;
use BenGorUser\UserBundle\Controller\RequestRememberPasswordController;
use BenGorUser\UserBundle\Form\Type\RequestRememberPasswordType;
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
use Symfony\Component\Translation\Translator;

/**
 * Spec file of RequestRememberPasswordController class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RequestRememberPasswordControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestRememberPasswordController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_renders_request_remember_password_action(
        Request $request,
        ContainerInterface $container,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RequestRememberPasswordType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/request_remember_password/request_remember_password.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->requestRememberPasswordAction($request, 'user')->shouldReturn($response);
    }

    function it_request_remember_password_action(
        Request $request,
        TransactionalApplicationService $service,
        RequestRememberPasswordRequest $rememberPasswordRequest,
        ContainerInterface $container,
        TwigEngine $templating,
        Session $session,
        FlashBagInterface $flashBag,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        Translator $translator
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(RequestRememberPasswordType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.request_user_remember_password')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($rememberPasswordRequest);
        $service->execute($rememberPasswordRequest)->shouldBeCalled();

        $container->get('translator')->shouldBeCalled()->willReturn($translator);
        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/request_remember_password/request_remember_password.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->requestRememberPasswordAction($request, 'user')->shouldReturn($response);
    }
}
