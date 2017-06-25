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

use BenGorUser\User\Application\Query\UserOfRememberPasswordTokenQuery;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Domain\Model\Exception\UserPasswordInvalidException;
use BenGorUser\User\Domain\Model\Exception\UserTokenExpiredException;
use BenGorUser\UserBundle\Form\FormErrorSerializer;
use BenGorUser\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType;
use BenGorUser\UserBundle\Form\Type\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Change password controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordController extends Controller
{
    /**
     * Default action, that it executes "default" specification of change password.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function defaultAction(Request $request, $userClass)
    {
        $form = $this->get('form.factory')->createNamedBuilder('', ChangePasswordType::class, null, [
            'csrf_protection' => false,
        ])->getForm();

        return $this->processForm($form, $request, $userClass);
    }

    /**
     * By request remember password action, that it executes
     * "by_request_remember_password" specification of change password.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function byRequestRememberPasswordAction(Request $request, $userClass)
    {
        $rememberPasswordToken = $request->query->get('token');
        try {
            // we need to know if the remember password token given exists in
            // database, in case that it isn't, it throws 404.
            $this->get('bengor_user.' . $userClass . '.by_remember_password_token_query')->__invoke(
                new UserOfRememberPasswordTokenQuery($rememberPasswordToken)
            );
        } catch (UserTokenExpiredException $exception) {
            return new JsonResponse(null, 404);
        } catch (UserDoesNotExistException $exception) {
            return new JsonResponse(null, 404);
        }

        $form = $this->get('form.factory')->createNamedBuilder(
            '',
            ChangePasswordByRequestRememberPasswordType::class,
            null,
            [
                'remember_password_token' => $rememberPasswordToken,
                'csrf_protection'         => false,
            ]
        )->getForm();

        return $this->processForm($form, $request, $userClass);
    }

    private function processForm(FormInterface $form, Request $request, $userClass)
    {
        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $this->get('bengor_user.' . $userClass . '.api_command_bus')->handle(
                    $form->getData()
                );

                return new JsonResponse();
            } catch (UserPasswordInvalidException $exception) {
                return new JsonResponse(
                    sprintf(
                        'The "%s" password is invalid',
                        $form->getData()->newPlainPassword()
                    ),
                    400
                );
            }
        }

        return new JsonResponse(FormErrorSerializer::errors($form), 400);
    }
}
