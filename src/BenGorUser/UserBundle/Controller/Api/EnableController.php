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

namespace BenGorUser\UserBundle\Controller\Api;

use BenGorUser\User\Application\Command\Enable\EnableUserCommand;
use BenGorUser\User\Domain\Model\Exception\UserTokenNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function enableAction(Request $request, $userClass)
    {
        $confirmationToken = $request->query->get('confirmation-token');
        if (null === $confirmationToken) {
            throw $this->createNotFoundException();
        }
        try {
            $this->get('bengor_user.' . $userClass . '.command_bus')->handle(
                new EnableUserCommand($confirmationToken)
            );
        } catch (UserTokenNotFoundException $exception) {
            return new JsonResponse(
                sprintf(
                    'The "%s" confirmation token does not exist',
                    $confirmationToken
                ),
                400
            );
        }

        return new JsonResponse();
    }
}
