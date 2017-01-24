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

use BenGorUser\User\Application\Command\RequestRememberPassword\RequestRememberPasswordCommand;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Controller\Api\RequestRememberPasswordController;
use BenGorUser\UserBundle\Form\Type\RequestRememberPasswordType;
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

    function it_request_remember_password_action(
        Request $request,
        UserCommandBus $commandBus,
        RequestRememberPasswordCommand $command,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormBuilderInterface $formBuilder
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', RequestRememberPasswordType::class, null, [
            'csrf_protection' => false,
        ])->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $form->getData()->shouldBeCalled()->willReturn($command);
        $commandBus->handle($command)->shouldBeCalled();

        $this->requestRememberPasswordAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_does_not_request_remember_password_action(
        Request $request,
        ContainerInterface $container,
        FormInterface $form,
        FormFactoryInterface $formFactory,
        FormBuilderInterface $formBuilder,
        FormError $error
    ) {
        $container->get('form.factory')->shouldBeCalled()->willReturn($formFactory);
        $formFactory->createNamedBuilder('', RequestRememberPasswordType::class, null, [
            'csrf_protection' => false,
        ])->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->isValid()->shouldBeCalled()->willReturn(false);

        $form->getErrors()->shouldBeCalled()->willReturn([$error]);
        $form->getName()->shouldBeCalled()->willReturn('');
        $form->rewind()->shouldBeCalled()->willReturn($form);
        $form->valid()->shouldBeCalled()->willReturn(false);

        $this->requestRememberPasswordAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
