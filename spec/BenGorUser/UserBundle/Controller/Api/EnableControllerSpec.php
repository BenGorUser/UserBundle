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

use BenGorUser\User\Application\Command\Enable\EnableUserCommand;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Controller\Api\EnableController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Spec file of EnableController class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class EnableControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EnableController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_does_not_enable_because_confirmation_token_is_not_provided(Request $request, ParameterBagInterface $bag)
    {
        $request->query = $bag;
        $bag->get('token')->shouldBeCalled()->willReturn(null);

        $this->enableAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }

    function it_enable_action(
        ContainerInterface $container,
        Request $request,
        ParameterBagInterface $bag,
        UserCommandBus $commandBus
    ) {
        $request->query = $bag;
        $bag->get('token')->shouldBeCalled()->willReturn('confirmation-token');

        $container->get('bengor_user.user.api_command_bus')->shouldBeCalled()->willReturn($commandBus);
        $commandBus->handle(Argument::type(EnableUserCommand::class))->shouldBeCalled();

        $this->enableAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
