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

namespace BenGor\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request The request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render('@BenGorUser/security/login.html.twig', [
            'last_email'  => $helper->getLastUsername(),
            'error'       => $helper->getLastAuthenticationError(),
            'login_check' => sprintf('%s_check', $request->attributes->get('_route'))
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
