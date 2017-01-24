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
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use BenGorUser\UserBundle\Controller\Api\JwtController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Spec file of JwtController class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class JwtControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JwtController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_new_token_action(
        UserCommandBus $commandBus,
        Request $request,
        ContainerInterface $container,
        JWTEncoderInterface $encoder
    ) {
        $request->getUser()->shouldBeCalled()->willReturn('bengor@user.com');
        $request->getPassword()->shouldBeCalled()->willReturn('123456');

        $container->get('bengor_user.user.command_bus')->shouldBeCalled()->willReturn($commandBus);
        $commandBus->handle(Argument::type(LogInUserCommand::class))->shouldBeCalled();

        $container->get('lexik_jwt_authentication.encoder.default')->shouldBeCalled()->willReturn($encoder);
        $encoder->encode(['email' => 'bengor@user.com'])->shouldBeCalled()->willReturn('the-token');

        $this->newTokenAction($request, 'user')->shouldReturnAnInstanceOf(JsonResponse::class);
    }
}
