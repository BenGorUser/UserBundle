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

namespace spec\BenGorUser\UserBundle\Controller\Api;

use BenGorUser\User\Application\Query\UserOfRememberPasswordTokenHandler;
use BenGorUser\User\Application\Query\UserOfRememberPasswordTokenQuery;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Command\ChangePasswordCommand;
use BenGorUser\UserBundle\Controller\Api\ChangePasswordController;
use BenGorUser\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType;
use BenGorUser\UserBundle\Form\Type\ChangePasswordType;
use PhpSpec\ObjectBehavior;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

    function it_default_action(
        FormBuilderInterface $formBuilder,
        UserCommandBus $commandBus,
        ChangePasswordCommand $command,
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            ChangePasswordType::class,
            null,
            ['csrf_protection' => false]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $commandBus->handle($command)->shouldBeCalled();

        $this->defaultAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_does_not_change_password_default_action(
        FormBuilderInterface $formBuilder,
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormError $error
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            ChangePasswordType::class,
            null,
            ['csrf_protection' => false]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->defaultAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_by_request_remember_password_action(
        UserCommandBus $commandBus,
        ChangePasswordCommand $command,
        UserOfRememberPasswordTokenHandler $handler,
        Request $request,
        ParameterBagInterface $bag,
        ContainerInterface $container,
        FormInterface $form,
        FormBuilderInterface $formBuilder,
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

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            ChangePasswordByRequestRememberPasswordType::class,
            null,
            [
                'remember_password_token' => 'remember-password-token',
                'csrf_protection'         => false,
            ]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);

        $commandBus->handle($command)->shouldBeCalled();

        $this->byRequestRememberPasswordAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_does_not_change_password_by_request_remember_password_action(
        UserOfRememberPasswordTokenHandler $handler,
        Request $request,
        ParameterBagInterface $bag,
        ContainerInterface $container,
        FormInterface $form,
        FormBuilderInterface $formBuilder,
        FormFactoryInterface $formFactory,
        FormError $error
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

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            ChangePasswordByRequestRememberPasswordType::class,
            null,
            [
                'remember_password_token' => 'remember-password-token',
                'csrf_protection'         => false,
            ]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->byRequestRememberPasswordAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
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

        $this->byRequestRememberPasswordAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
