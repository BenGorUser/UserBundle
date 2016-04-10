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
 * Remove user controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RemoveController extends Controller
{
    /**
     * Remove ser password action.
     *
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name, by default is null
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request, $userClass, $successRoute = null)
    {
        // @Todo
    }
}
