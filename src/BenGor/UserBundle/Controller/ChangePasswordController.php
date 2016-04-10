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
 * Change password controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordController extends Controller
{
    /**
     * Changes user password action.
     *
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name, by default is null
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request, $userClass, $successRoute = null)
    {
        // @Todo
    }
}
