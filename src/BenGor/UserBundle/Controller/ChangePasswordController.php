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

use BenGor\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGor\User\Domain\Model\Exception\UserPasswordInvalidException;
use BenGor\User\Domain\Model\UserToken;
use BenGor\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType;
use BenGor\UserBundle\Form\Type\ChangePasswordType;
use BenGor\UserBundle\Model\User;
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
                $service = $this->get('bengor_user.change_' . $userClass . '_password');

                try {
                    $service->execute($form->getData());
                    $this->addFlash('notice', 'The password is successfully changed');

                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserPasswordInvalidException $exception) {
                    $this->addFlash('error', 'The current password is not correct');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/change_password/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
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
        $user = $this->get('bengor_user.' . $userClass . '_repository')
            ->userOfRememberPasswordToken(new UserToken($rememberPasswordToken));
        if (!$user instanceof User) {
            throw $this->createNotFoundException('Remember password token does not exist');
        }
        $form = $this->createForm(ChangePasswordByRequestRememberPasswordType::class, null, [
            'remember_password_token' => $rememberPasswordToken,
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->get('bengor_user.change_' . $userClass . '_password');

                try {
                    $service->execute($form->getData());
                    $this->addFlash('notice', 'The password is successfully changed');

                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserDoesNotExistException $exception) {
                    $this->addFlash('error', 'Remember password token does not exist');
                } catch (UserPasswordInvalidException $exception) {
                    $this->addFlash('error', 'The current password is not correct');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/change_password/by_request_remember_password.html.twig', [
            'form'  => $form->createView(),
            'email' => $user->email()->email(),
        ]);
    }
}
