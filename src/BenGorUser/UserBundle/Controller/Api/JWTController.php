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

use BenGorUser\User\Application\Command\LogIn\LogInUserCommand;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Domain\Model\Exception\UserEmailInvalidException;
use BenGorUser\User\Domain\Model\Exception\UserInactiveException;
use BenGorUser\User\Domain\Model\Exception\UserPasswordInvalidException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * JWT controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class JWTController extends Controller
{
    /**
     * Generates new token action.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newTokenAction(Request $request, $userClass)
    {
        try {
            $this->get('bengor_user.' . $userClass . '.command_bus')->handle(
                new LogInUserCommand(
                    $request->getUser(),
                    $request->getPassword()
                )
            );
        } catch (UserDoesNotExistException $exception) {
            return new JsonResponse('', 400);
        } catch (UserEmailInvalidException $exception) {
            return new JsonResponse('', 400);
        } catch (UserInactiveException $exception) {
            return new JsonResponse('Inactive user', 400);
        } catch (UserPasswordInvalidException $exception) {
            return new JsonResponse('', 400);
        }
        $token = $this->get('lexik_jwt_authentication.encoder')->encode(['email' => $request->getUser()]);

        return new JsonResponse(['token' => $token]);
    }
}
