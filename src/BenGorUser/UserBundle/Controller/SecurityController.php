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

namespace BenGorUser\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Security controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityController extends Controller
{
    /**
     * Login action.
     *
     * @param Request $request      The request
     * @param string  $successRoute Extra parameter that contains the success route name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request, $successRoute)
    {
        if ($this->get('security.authorization_checker')->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            return $this->redirectToRoute($successRoute);
        }
        $helper = $this->get('security.authentication_utils');

        return $this->render('@BenGorUser/security/login.html.twig', [
            'last_email'  => $helper->getLastUsername(),
            'error'       => $helper->getLastAuthenticationError(),
            'login_check' => sprintf('%s_check', $request->attributes->get('_route')),
        ]);
    }

    /**
     * Login check action.
     */
    public function loginCheckAction()
    {
    }

    /**
     * Logout action.
     */
    public function logoutAction()
    {
    }
}
