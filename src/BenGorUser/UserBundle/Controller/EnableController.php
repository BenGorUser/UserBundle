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

use BenGorUser\User\Application\Command\Enable\EnableUserCommand;
use BenGorUser\User\Domain\Model\Exception\UserTokenNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Enable user controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class EnableController extends Controller
{
    /**
     * Enable user action.
     *
     * @param Request $request      The request
     * @param string  $userClass    Extra parameter that contains the user type
     * @param string  $successRoute Extra parameter that contains the success route name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enableAction(Request $request, $userClass, $successRoute)
    {
        $confirmationToken = $request->query->get('token');
        if (null === $confirmationToken) {
            throw $this->createNotFoundException();
        }
        try {
            $this->get('bengor_user.' . $userClass . '.command_bus')->handle(
                new EnableUserCommand($confirmationToken)
            );
            $this->addFlash('notice', $this->get('translator')->trans(
                'enable.success_flash', [], 'BenGorUser'
            ));
        } catch (UserTokenNotFoundException $exception) {
            $this->addFlash('error', $this->get('translator')->trans(
                'enable.error_flash_user_token_not_found', [], 'BenGorUser'
            ));
        }

        return $this->redirectToRoute($successRoute);
    }
}
