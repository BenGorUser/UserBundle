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

use BenGorUser\User\Application\Command\Invite\InviteUserCommand;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Controller\Api\InviteController;
use BenGorUser\UserBundle\Form\Type\InviteType;
use BenGorUser\UserBundle\Form\Type\ResendInvitationType;
use PhpSpec\ObjectBehavior;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

    function it_invites_action(
        UserCommandBus $commandBus,
        InviteUserCommand $command,
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormBuilderInterface $formBuilder
    ) {
        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', InviteType::class, null, [
            'roles'           => ['ROLE_USER'],
            'csrf_protection' => false,
        ])->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.api_command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $commandBus->handle($command)->shouldBeCalled();
        $command->id()->shouldBeCalled()->willReturn('user-id');
        $command->email()->shouldBeCalled()->willReturn('bengor@user.com');
        $command->roles()->shouldBeCalled()->willReturn(['ROLE_USER']);

        $this->inviteAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_does_not_invite_action(
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormBuilderInterface $formBuilder,
        FormError $error
    ) {
        $container->getParameter('bengor_user.user_default_roles')->shouldBeCalled()->willReturn(['ROLE_USER']);

        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', InviteType::class, null, [
            'roles'           => ['ROLE_USER'],
            'csrf_protection' => false,
        ])->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->inviteAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_resends_invitation_action(
        UserCommandBus $commandBus,
        InviteUserCommand $command,
        Request $request,
        ContainerInterface $container,
        FormBuilderInterface $formBuilder,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', ResendInvitationType::class, null, [
            'csrf_protection' => false,
        ])->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.api_command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $commandBus->handle($command)->shouldBeCalled();

        $this->resendInvitationAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_does_not_resend_invitation_action(
        Request $request,
        ContainerInterface $container,
        FormError $error,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormBuilderInterface $formBuilder
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', ResendInvitationType::class, null, [
            'csrf_protection' => false,
        ])->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->resendInvitationAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
