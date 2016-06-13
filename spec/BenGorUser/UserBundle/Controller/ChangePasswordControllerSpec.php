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

use BenGorUser\User\Application\Query\UserOfRememberPasswordTokenHandler;
use BenGorUser\User\Application\Query\UserOfRememberPasswordTokenQuery;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Command\ChangePasswordCommand;
use BenGorUser\UserBundle\Controller\ChangePasswordController;
use BenGorUser\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType;
use BenGorUser\UserBundle\Form\Type\ChangePasswordType;
use BenGorUser\UserBundle\Security\UserSymfonyDataTransformer;
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
use Symfony\Component\Security\Core\User\UserInterface;
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
        $templating->renderResponse('@BenGorUser/change_password/default.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->defaultAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_default_action(
        UserCommandBus $commandBus,
        ChangePasswordCommand $command,
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

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $commandBus->handle($command)->shouldBeCalled();

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
        $templating->renderResponse('@BenGorUser/change_password/default.html.twig', [
            'form' => $formView,
        ], null)->shouldBeCalled()->willReturn($response);

        $this->defaultAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_renders_by_request_remember_password_action(
        ParameterBagInterface $bag,
        Request $request,
        UserOfRememberPasswordTokenHandler $handler,
        UserSymfonyDataTransformer $dataTransformer,
        UserInterface $user,
        ContainerInterface $container,
        TwigEngine $templating,
        Response $response,
        FormView $formView,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $rememberPasswordTokenQuery = new UserOfRememberPasswordTokenQuery('remember-password-token');
        $userDto = [
            'email'    => 'bengor@user.com',
            'password' => '123456',
            'roles'    => ['ROLE_USER', 'ROLE_ADMIN'],
        ];
        $container->get('bengor_user.user.by_remember_password_token_query')->shouldBeCalled()->willReturn($handler);
        $handler->__invoke($rememberPasswordTokenQuery)->shouldBeCalled()->willReturn($userDto);
        $container->get('bengor_user.user.symfony_data_transformer')->shouldBeCalled()->willReturn($dataTransformer);
        $dataTransformer->write($userDto)->shouldBeCalled();
        $dataTransformer->read()->shouldBeCalled()->willReturn($user);

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
        $user->getUsername()->shouldBeCalled()->willReturn('bengor@user.com');
        $templating->renderResponse('@BenGorUser/change_password/by_request_remember_password.html.twig', [
            'form'  => $formView,
            'email' => 'bengor@user.com',
        ], null)->shouldBeCalled()->willReturn($response);

        $this->byRequestRememberPasswordAction($request, 'user', 'bengor_user_user_homepage')->shouldReturn($response);
    }

    function it_by_request_remember_password_action(
        UserCommandBus $commandBus,
        ChangePasswordCommand $command,
        UserOfRememberPasswordTokenHandler $handler,
        UserSymfonyDataTransformer $dataTransformer,
        Request $request,
        ParameterBagInterface $bag,
        ContainerInterface $container,
        Session $session,
        Router $router,
        FlashBagInterface $flashBag,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        UserInterface $user,
        Translator $translator
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $rememberPasswordTokenQuery = new UserOfRememberPasswordTokenQuery('remember-password-token');
        $userDto = [
            'email'    => 'bengor@user.com',
            'password' => '123456',
            'roles'    => ['ROLE_USER', 'ROLE_ADMIN'],
        ];
        $container->get('bengor_user.user.by_remember_password_token_query')->shouldBeCalled()->willReturn($handler);
        $handler->__invoke($rememberPasswordTokenQuery)->shouldBeCalled()->willReturn($userDto);
        $container->get('bengor_user.user.symfony_data_transformer')->shouldBeCalled()->willReturn($dataTransformer);
        $dataTransformer->write($userDto)->shouldBeCalled();
        $dataTransformer->read()->shouldBeCalled()->willReturn($user);

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->create(
            ChangePasswordByRequestRememberPasswordType::class, null, [
                'remember_password_token' => 'remember-password-token',
            ]
        )->shouldBeCalled()->willReturn($form);

        $request->isMethod('POST')->shouldBeCalled()->willReturn(true);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);

        $commandBus->handle($command)->shouldBeCalled();

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
        UserOfRememberPasswordTokenHandler $handler,
        UserSymfonyDataTransformer $dataTransformer,
        Request $request,
        ParameterBagInterface $bag,
        ContainerInterface $container,
        FormView $formView,
        Response $response,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        UserInterface $user
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $rememberPasswordTokenQuery = new UserOfRememberPasswordTokenQuery('remember-password-token');
        $userDto = [
            'email'    => 'bengor@user.com',
            'password' => '123456',
            'roles'    => ['ROLE_USER', 'ROLE_ADMIN'],
        ];
        $container->get('bengor_user.user.by_remember_password_token_query')->shouldBeCalled()->willReturn($handler);
        $handler->__invoke($rememberPasswordTokenQuery)->shouldBeCalled()->willReturn($userDto);
        $container->get('bengor_user.user.symfony_data_transformer')->shouldBeCalled()->willReturn($dataTransformer);
        $dataTransformer->write($userDto)->shouldBeCalled();
        $dataTransformer->read()->shouldBeCalled()->willReturn($user);

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
        $user->getUsername()->shouldBeCalled()->willReturn('bengor@user.com');
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
        UserOfRememberPasswordTokenHandler $handler
    ) {
        $request->query = $bag;
        $bag->get('remember-password-token')->shouldBeCalled()->willReturn('remember-password-token');
        $rememberPasswordTokenQuery = new UserOfRememberPasswordTokenQuery('remember-password-token');
        $container->get('bengor_user.user.by_remember_password_token_query')->shouldBeCalled()->willReturn($handler);
        $handler->__invoke($rememberPasswordTokenQuery)->shouldBeCalled()->willThrow(UserDoesNotExistException::class);

        $this->shouldThrow(NotFoundHttpException::class)->duringByRequestRememberPasswordAction(
            $request, 'user', 'bengor_user_user_homepage'
        );
    }
}
