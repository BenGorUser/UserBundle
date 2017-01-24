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

use BenGorUser\User\Application\Command\LogIn\LogInUserCommand;
use BenGorUser\User\Application\Command\SignUp\ByInvitationSignUpUserCommand;
use BenGorUser\User\Application\Command\SignUp\SignUpUserCommand;
use BenGorUser\User\Application\Query\UserOfInvitationTokenHandler;
use BenGorUser\User\Application\Query\UserOfInvitationTokenQuery;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Controller\Api\SignUpController;
use BenGorUser\UserBundle\Form\Type\SignUpByInvitationType;
use BenGorUser\UserBundle\Form\Type\SignUpType;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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

    function it_default_action(
        FormBuilderInterface $formBuilder,
        UserCommandBus $commandBus,
        SignUpUserCommand $command,
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormInterface $formChild,
        FormFactoryInterface $formFactory,
        JWTEncoderInterface $encoder,
        UserProviderInterface $userProvider
    ) {
        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            SignUpType::class,
            null,
            ['csrf_protection' => false, 'roles' => ['ROLE_USER']]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $command->email()->shouldBeCalled()->willReturn('bengor@user.com');
        $command->password()->shouldBeCalled()->willReturn('123456');
        $commandBus->handle($command)->shouldBeCalled();
        $commandBus->handle(Argument::type(LogInUserCommand::class))->shouldBeCalled();

        $container->get('lexik_jwt_authentication.encoder.default')->shouldBeCalled()->willReturn($encoder);
        $encoder->encode(['email' => 'bengor@user.com'])->shouldBeCalled()->willReturn('the-token');

        $this->defaultAction($request, 'user', SignUpType::class)->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_does_not_sign_up_default_action(
        FormBuilderInterface $formBuilder,
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormError $error
    ) {
        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            SignUpType::class,
            null,
            ['csrf_protection' => false, 'roles' => ['ROLE_USER']]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->defaultAction($request, 'user', SignUpType::class)->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_by_invitation_action(
        ParameterBagInterface $bag,
        ByInvitationSignUpUserCommand $command,
        UserCommandBus $commandBus,
        UserOfInvitationTokenHandler $queryHandler,
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormBuilderInterface $formBuilder,
        JWTEncoderInterface $encoder
    ) {
        $bag->get('invitation-token')->willReturn('invitation-token');
        $request->query = $bag;

        $invitationTokenQuery = new UserOfInvitationTokenQuery('invitation-token');
        $userDto = [
            'email'    => 'bengor@user.com',
            'password' => '123456',
            'roles'    => ['ROLE_USER', 'ROLE_ADMIN'],
        ];
        $container->get('bengor_user.user.by_invitation_token_query')->shouldBeCalled()->willReturn($queryHandler);
        $queryHandler->__invoke($invitationTokenQuery)->shouldBeCalled()->willReturn($userDto);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            SignUpByInvitationType::class,
            null,
            [
                'csrf_protection'  => false,
                'roles'            => ['ROLE_USER'],
                'invitation_token' => 'invitation-token',
            ]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $commandBus->handle($command)->shouldBeCalled();
        $command->password()->shouldBeCalled()->willReturn('123456');

        $commandBus->handle(Argument::type(LogInUserCommand::class))->shouldBeCalled();

        $container->get('lexik_jwt_authentication.encoder.default')->shouldBeCalled()->willReturn($encoder);
        $encoder->encode(['email' => 'bengor@user.com'])->shouldBeCalled()->willReturn('the-token');

        $this->byInvitationAction($request, 'user', SignUpByInvitationType::class)->shouldReturnAnInstanceOf(
            JsonResponse::class
        );
    }

    function it_does_not_sign_up_by_invitation_action(
        ParameterBagInterface $bag,
        UserOfInvitationTokenHandler $queryHandler,
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormBuilderInterface $formBuilder,
        FormError $error
    ) {
        $bag->get('invitation-token')->willReturn('invitation-token');
        $request->query = $bag;

        $invitationTokenQuery = new UserOfInvitationTokenQuery('invitation-token');
        $userDto = [
            'email'    => 'bengor@user.com',
            'password' => '123456',
            'roles'    => ['ROLE_USER', 'ROLE_ADMIN'],
        ];
        $container->get('bengor_user.user.by_invitation_token_query')->shouldBeCalled()->willReturn($queryHandler);
        $queryHandler->__invoke($invitationTokenQuery)->shouldBeCalled()->willReturn($userDto);

        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder(
            '',
            SignUpByInvitationType::class,
            null,
            [
                'roles'            => ['ROLE_USER'],
                'invitation_token' => 'invitation-token',
                'csrf_protection'  => false,
            ]
        )->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->byInvitationAction($request, 'user', SignUpByInvitationType::class)->shouldReturnAnInstanceOf(
            JsonResponse::class
        );
    }

    function it_does_not_render_because_invitation_token_does_not_exist(
        ParameterBagInterface $bag,
        Request $request,
        ContainerInterface $container,
        UserOfInvitationTokenHandler $handler
    ) {
        $bag->get('invitation-token')->willReturn('invitation-token');
        $request->query = $bag;

        $invitationTokenQuery = new UserOfInvitationTokenQuery('invitation-token');
        $container->get('bengor_user.user.by_invitation_token_query')->shouldBeCalled()->willReturn($handler);
        $handler->__invoke($invitationTokenQuery)->shouldBeCalled()->willThrow(UserDoesNotExistException::class);

        $this->shouldThrow(NotFoundHttpException::class)->duringByInvitationAction(
            $request, 'user', SignUpByInvitationType::class
        );
    }
}
