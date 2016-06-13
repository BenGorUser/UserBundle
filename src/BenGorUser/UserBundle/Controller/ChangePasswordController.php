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

use BenGorUser\User\Application\Query\UserOfRememberPasswordTokenQuery;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Domain\Model\Exception\UserPasswordInvalidException;
use BenGorUser\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType;
use BenGorUser\UserBundle\Form\Type\ChangePasswordType;
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
     * Default action, that it executes "default" specification of change password.
     *
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name, by default is null
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request, $userClass, $successRoute = null)
    {
        $form = $this->createForm(ChangePasswordType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor_user.' . $userClass . '.command_bus')->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('change_password.success_flash'));
                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserPasswordInvalidException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans(
                        'change_password.error_flash_user_password_invalid'
                    ));
                }
            }
        }

        return $this->render('@BenGorUser/change_password/default.html.twig', ['form' => $form->createView()]);
    }

    /**
     * By request remember password action, that it executes
     * "by_request_remember_password" specification of change password.
     *
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name, null by default
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function byRequestRememberPasswordAction(Request $request, $userClass, $successRoute = null)
    {
        $rememberPasswordToken = $request->query->get('remember-password-token');
        try {
            // we need to know if the remember password token given exists in
            // database, in case that it isn't, it throws 404.
            $user = $this->get('bengor_user.' . $userClass . '.by_remember_password_token_query')->__invoke(
                new UserOfRememberPasswordTokenQuery($rememberPasswordToken)
            );

            // Convert to an object implementing Symfony's UserInterface
            $dataTransformer = $this->get('bengor_user.' . $userClass . '.symfony_data_transformer');
            $dataTransformer->write($user);
            $user = $dataTransformer->read();
        } catch (UserDoesNotExistException $exception) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ChangePasswordByRequestRememberPasswordType::class, null, [
            'remember_password_token' => $rememberPasswordToken,
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor_user.' . $userClass . '.command_bus')->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('change_password.success_flash'));
                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserPasswordInvalidException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans(
                        'change_password.error_flash_user_password_invalid'
                    ));
                }
            }
        }

        return $this->render('@BenGorUser/change_password/by_request_remember_password.html.twig', [
            'form'  => $form->createView(),
            'email' => $user->getUsername(),
        ]);
    }
}
