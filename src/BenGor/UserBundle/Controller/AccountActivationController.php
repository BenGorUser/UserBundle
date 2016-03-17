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

use BenGor\User\Application\Service\ActivateUserAccountRequest;
use BenGor\User\Domain\Model\Exception\UserTokenNotFoundException;
use BenGor\User\Domain\Model\UserGuest;
use BenGor\User\Domain\Model\UserToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Account activation controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class AccountActivationController extends Controller
{
    /**
     * Activate account action.
     *
     * @param Request $request      The request
     * @param string  $userClass    Extra parameter that contains the user type
     * @param string  $successRoute Extra parameter that contains the success route name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activateAccountAction(Request $request, $userClass, $successRoute)
    {
        $confirmationToken = $request->query->get('confirmation-token');
        if (null === $confirmationToken) {
            throw $this->createNotFoundException();
        }

        try {
            $this->get('bengor_user.activate_' . $userClass . '_account')->execute(
                new ActivateUserAccountRequest(
                    $confirmationToken
                )
            );
            $this->addFlash('notice', 'Account is successfully activated');
        } catch (UserTokenNotFoundException $exception) {
            $this->addFlash('error', 'The confirmation token is invalid');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
        }

        return $this->redirectToRoute($successRoute);
    }
}
