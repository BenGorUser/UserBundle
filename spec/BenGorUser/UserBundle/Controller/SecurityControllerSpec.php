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

use BenGorUser\UserBundle\Controller\SecurityController;
use PhpSpec\ObjectBehavior;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Spec file of security controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SecurityController::class);
    }

    function it_extends_controller()
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_login_action(
        Request $request,
        ContainerInterface $container,
        AuthorizationCheckerInterface $authorizationChecker,
        AuthenticationUtils $helper,
        ParameterBagInterface $parameterBag,
        TwigEngine $templating,
        Response $response
    ) {
        $container->get('security.authorization_checker')->shouldBeCalled()->willReturn($authorizationChecker);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')
            ->shouldBeCalled()->willReturn(false);

        $container->get('security.authentication_utils')->shouldBeCalled()->willReturn($helper);
        $helper->getLastUsername()->shouldBeCalled()->willReturn('last@email.com');
        $helper->getLastAuthenticationError()->shouldBeCalled()->willReturn('error');
        $parameterBag->get('_route')->shouldBeCalled()->willReturn('login');
        $request->attributes = $parameterBag;

        $container->has('templating')->shouldBeCalled()->willReturn(true);
        $container->get('templating')->shouldBeCalled()->willReturn($templating);
        $templating->renderResponse('@BenGorUser/security/login.html.twig', [
            'last_email'  => 'last@email.com',
            'error'       => 'error',
            'login_check' => 'login_check',
        ], null)->shouldBeCalled()->willReturn($response);

        $this->loginAction($request, 'admin')->shouldReturn($response);
    }

    function it_is_already_logged_action(
        Request $request,
        ContainerInterface $container,
        AuthorizationCheckerInterface $authorizationChecker,
        Router $router
    ) {
        $container->get('security.authorization_checker')->shouldBeCalled()->willReturn($authorizationChecker);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')->shouldBeCalled()->willReturn(true);

        $container->get('router')->shouldBeCalled()->willReturn($router);
        $router->generate('admin', [], 1)->shouldBeCalled()->willReturn('/');

        $this->loginAction($request, 'admin');
    }

    function it_login_check_action()
    {
        $this->loginCheckAction();
    }

    function it_logout_action()
    {
        $this->logoutAction();
    }
}
