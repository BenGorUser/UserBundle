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

use BenGorUser\User\Application\Service\Enable\EnableUserRequest;
use BenGorUser\UserBundle\Controller\EnableController;
use Ddd\Application\Service\TransactionalApplicationService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;

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
        $bag->get('confirmation-token')->shouldBeCalled()->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->duringEnableAction(
            $request, 'user', 'bengor_user_user_homepage'
        );
    }

    function it_enable_action(
        ContainerInterface $container,
        Request $request,
        ParameterBagInterface $bag,
        TransactionalApplicationService $service,
        Session $session,
        FlashBagInterface $flashBag,
        Router $router,
        Translator $translator
    ) {
        $request->query = $bag;
        $bag->get('confirmation-token')->shouldBeCalled()->willReturn('confirmation-token');

        $container->get('bengor_user.enable_user')->shouldBeCalled()->willReturn($service);
        $service->execute(Argument::type(EnableUserRequest::class))->shouldBeCalled();

        $container->get('translator')->shouldBeCalled()->willReturn($translator);
        $container->has('session')->shouldBeCalled()->willReturn(true);
        $container->get('session')->shouldBeCalled()->willReturn($session);
        $session->getFlashBag()->shouldBeCalled()->willReturn($flashBag);

        $container->get('router')->shouldBeCalled()->willReturn($router);
        $router->generate('bengor_user_user_homepage', [], 1)->shouldBeCalled()->willReturn('/');

        $this->enableAction($request, 'user', 'bengor_user_user_homepage');
    }
}
