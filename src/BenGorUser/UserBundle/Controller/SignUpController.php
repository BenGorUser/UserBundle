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

use BenGorUser\User\Application\Query\UserOfInvitationTokenQuery;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\UserBundle\Form\Type\SignUpByInvitationType;
use BenGorUser\UserBundle\Form\Type\SignUpType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sign up user controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpController extends Controller
{
    /**
     * Default action, that it can executes the "default"
     * or "with_confirmation" specifications.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     * @param string  $firewall  Extra parameter that contains the firewall name
     * @param string  $command   Extra parameter that contains the fully qualified class name of command
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request, $userClass, $firewall, $command)
    {
        $roles = $this->getParameter('bengor_user.' . $userClass . '_default_roles');
        $form = $this->createForm(SignUpType::class, null, ['roles' => $roles, 'command' => $command]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor_user.' . $userClass . '_command_bus')->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('sign_up.success_flash'));

                    return $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $this->get('bengor_user.' . $userClass . '_provider')->loadUserByUsername(
                                $form->get('email')->getData()
                            ),
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                } catch (\Exception $exception) {
                    $this->get('logger')->addError($exception->getMessage());
                    $this->addFlash('error', $this->get('translator')->trans('sign_up.error_flash_generic'));
                }
            }
        }

        return $this->render('@BenGorUser/sign_up/default.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * By invitation action, that it can executes the "by_invitation"
     * or "by_invitation_with_confirmation" specifications.
     *
     * @param Request $request         The request
     * @param string  $invitationToken The invitation token
     * @param string  $userClass       Extra parameter that contains the user type
     * @param string  $firewall        Extra parameter that contains the firewall name
     * @param string  $command         Extra parameter that contains the fully qualified class name of command
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function byInvitationAction(Request $request, $invitationToken, $userClass, $firewall, $command)
    {
        try {
            // we need to know if the invitation token given exists in
            // database, in case that it isn't, it throws 404.
            $user = $this->get('bengor_user.' . $userClass . '_invitation_token_query')->__invoke(
                new UserOfInvitationTokenQuery($invitationToken)
            );
            $dataTransformer = $this->get('bengor_user.user_symfony_data_transformer');
            $dataTransformer->write($user);
            $user = $dataTransformer->read();
        } catch (UserDoesNotExistException $exception) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(SignUpByInvitationType::class, null, [
            'roles'            => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
            'invitation_token' => $invitationToken,
            'command'          => $command,
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor_user.' . $userClass . '_command_bus')->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('sign_up.success_flash'));

                    return $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                } catch (\Exception $exception) {
                    $this->get('logger')->addError($exception->getMessage());
                    $this->addFlash('error', $this->get('translator')->trans('sign_up.error_flash_generic'));
                }
            }
        }

        return $this->render('@BenGorUser/sign_up/by_invitation.html.twig', [
            'email' => $user->getUsername(),
            'form'  => $form->createView(),
        ]);
    }
}
