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

use BenGorUser\User\Application\Command\Remove\RemoveUserCommand;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Controller\Api\RemoveController;
use BenGorUser\UserBundle\Form\Type\RemoveType;
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

    function it_removes_action(
        UserCommandBus $commandBus,
        RemoveUserCommand $command,
        Request $request,
        ContainerInterface $container,
        FormBuilderInterface $formBuilder,
        FormInterface $form,
        FormFactoryInterface $formFactory
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', RemoveType::class, null, ['csrf_protection' => false])
            ->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.api_command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $commandBus->handle($command)->shouldBeCalled();

        $this->removeAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_does_not_remove_action(
        Request $request,
        ContainerInterface $container,
        FormBuilderInterface $formBuilder,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormError $error
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', RemoveType::class, null, ['csrf_protection' => false])
            ->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->removeAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
