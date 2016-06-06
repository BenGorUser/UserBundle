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

namespace spec\BenGorUser\UserBundle\Controller;

use BenGorUser\User\Application\Service\ChangePassword\ChangeUserPasswordRequest;
use BenGorUser\User\Application\Service\ChangePassword\ChangeUserPasswordService;
use BenGorUser\User\Domain\Model\UserEmail;
use BenGorUser\User\Domain\Model\UserRepository;
use BenGorUser\User\Domain\Model\UserToken;
use BenGorUser\UserBundle\Controller\ChangePasswordController;
use BenGorUser\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType;
use BenGorUser\UserBundle\Form\Type\ChangePasswordType;
use BenGorUser\UserBundle\Model\User;
use Ddd\Application\Service\TransactionalApplicationService;
use PhpSpec\ObjectBehavior;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;

/**
 * Spec file of ChangePasswordController class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChangePasswordController::class);
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
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(ChangePasswordType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/change_password/change_password.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->defaultAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_default_action(
        TransactionalApplicationService $service,
        ChangeUserPasswordRequest $changeUserPasswordRequest,
        Request $request,
        ContainerInterface $container,
        Session $session,
        FlashBagInterface $flashBag,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        Router $router,
        Translator $translator
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(ChangePasswordType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.change_user_password')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($changeUserPasswordRequest);

        $service->execute($changeUserPasswordRequest)->shouldBeCalled();

        $container->get('translator')->shouldBeCalled()->willReturn($translator);
        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->get('router')->shouldBeCalled()->willReturn($router);
        $router->generate(
            'bengor_user_user_homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH
        )->shouldBeCalled()->willReturn('/');

        $this->defaultAction(
            $request, 'user', 'bengor_user_user_homepage'
        )->shouldReturnAnInstanceOf(RedirectResponse::class);
    }

    function it_does_not_change_password_default_action(
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormView $formView,
        TwigEngine $templating,
        Response $response
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(ChangePasswordType::class, null, [])->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/change_password/change_password.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->defaultAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_renders_by_request_remember_password_action(
        ParameterBagInterface $bag,
        Request $request,
        ContainerInterface $container,
        UserRepository $repository,
        TwigEngine $templating,
        User $user,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $container->get('bengor_user.user_repository')->shouldBeCalled()->willReturn($repository);
        $repository->userOfRememberPasswordToken(
            new UserToken('remember-password-token')
        )->shouldBeCalled()->willReturn($user);
        $user->email()->shouldBeCalled()->willReturn(new UserEmail('bengor@user.com'));
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(
            ChangePasswordByRequestRememberPasswordType::class, null, [
                'remember_password_token' => 'remember-password-token',
            ]
        )->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/change_password/by_request_remember_password.html.twig', [
            'form'  => $formView,
            'email' => 'bengor@user.com',
        ], null)->shouldBeCalled()->willReturn($response);

        $this->byRequestRememberPasswordAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_by_request_remember_password_action(
        ChangeUserPasswordService $service,
        ChangeUserPasswordRequest $changeUserPasswordRequest,
        Request $request,
        ParameterBagInterface $bag,
        ContainerInterface $container,
        Session $session,
        Router $router,
        FlashBagInterface $flashBag,
        UserRepository $repository,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        User $user,
        Translator $translator
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $container->get('bengor_user.user_repository')->shouldBeCalled()->willReturn($repository);
        $repository->userOfRememberPasswordToken(
            new UserToken('remember-password-token')
        )->shouldBeCalled()->willReturn($user);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(
            ChangePasswordByRequestRememberPasswordType::class, null, [
                'remember_password_token' => 'remember-password-token',
            ]
        )->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.change_user_password')->shouldBeCalled()->willReturn($service);
        $form->getData()->shouldBeCalled()->willReturn($changeUserPasswordRequest);

        $service->execute($changeUserPasswordRequest)->shouldBeCalled();

        $container->get('translator')->shouldBeCalled()->willReturn($translator);
        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->get('router')->shouldBeCalled()->willReturn($router);
        $router->generate(
            'bengor_user_user_homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH
        )->shouldBeCalled()->willReturn('/');

        $this->byRequestRememberPasswordAction(
            $request, 'user', 'bengor_user_user_homepage'
        )->shouldReturnAnInstanceOf(RedirectResponse::class);
    }

    function it_does_not_change_password_by_request_remember_password_action(
        TwigEngine $templating,
        Request $request,
        ParameterBagInterface $bag,
        ContainerInterface $container,
        FormView $formView,
        Response $response,
        UserRepository $repository,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        User $user
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $container->get('bengor_user.user_repository')->shouldBeCalled()->willReturn($repository);
        $repository->userOfRememberPasswordToken(
            new UserToken('remember-password-token')
        )->shouldBeCalled()->willReturn($user);
        $user->email()->shouldBeCalled()->willReturn(new UserEmail('bengor@user.com'));
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(
            ChangePasswordByRequestRememberPasswordType::class, null, [
                'remember_password_token' => 'remember-password-token',
            ]
        )->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $form->createView()->shouldBeCalled()->willReturn($formView);
        $templating->renderResponse('@BenGorUser/change_password/by_request_remember_password.html.twig', [
            'form'  => $formView,
            'email' => 'bengor@user.com',
        ], null)->shouldBeCalled()->willReturn($response);

        $this->byRequestRememberPasswordAction(
            $request, 'user', 'bengor_user_user_homepage'
        )->shouldReturn($response);
    }

    function it_does_not_change_password_because_token_does_not_exist(
        ParameterBagInterface $bag,
        Request $request,
        ContainerInterface $container,
        UserRepository $repository
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $container->get('bengor_user.user_repository')->shouldBeCalled()->willReturn($repository);
        $repository->userOfRememberPasswordToken(
            new UserToken('remember-password-token')
        )->shouldBeCalled()->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->duringByRequestRememberPasswordAction(
            $request, 'user', 'bengor_user_user_homepage'
        );
    }
}
