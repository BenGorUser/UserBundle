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
use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\UserBundle\Form\Type\SignUpByInvitationType;
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
     * @param string  $formType  Extra parameter that contains the form type FQCN
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request, $userClass, $firewall, $formType)
    {
        $roles = $this->getParameter('bengor_user.' . $userClass . '_default_roles');
        $form = $this->createForm($formType, null, ['roles' => $roles]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor_user.' . $userClass . '.command_bus')->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('sign_up.success_flash'));

                    return $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $this->get('bengor_user.' . $userClass . '.provider')->loadUserByUsername(
                                $form->get('email')->getData()
                            ),
                            $request,
                            $this->get('bengor_user.' . $userClass . '.form_login_authenticator'),
                            $firewall
                        );
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('sign_up.error_flash_user_already_exist'));
                }
            }
        }

        return $this->render('@BenGorUser/sign_up/default.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * By invitation action, that it can executes the "by_invitation" specification.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     * @param string  $firewall  Extra parameter that contains the firewall name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function byInvitationAction(Request $request, $userClass, $firewall)
    {
        $invitationToken = $request->query->get('invitation-token');
        try {
            // we need to know if the invitation token given exists in
            // database, in case that it isn't, it throws 404.
            $user = $this->get('bengor_user.' . $userClass . '.by_invitation_token_query')->__invoke(
                new UserOfInvitationTokenQuery($invitationToken)
            );

            // Convert to an object implementing Symfony's UserInterface
            $dataTransformer = $this->get('bengor_user.' . $userClass . '.symfony_data_transformer');
            $dataTransformer->write($user);
            $user = $dataTransformer->read();
        } catch (UserDoesNotExistException $exception) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(SignUpByInvitationType::class, null, [
            'roles'            => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
            'invitation_token' => $invitationToken,
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->get('bengor_user.' . $userClass . '.command_bus')->handle($form->getData());
                $this->addFlash('notice', $this->get('translator')->trans('sign_up.success_flash'));

                return $this
                    ->get('security.authentication.guard_handler')
                    ->authenticateUserAndHandleSuccess(
                        $user,
                        $request,
                        $this->get('bengor_user.' . $userClass . '.form_login_authenticator'),
                        $firewall
                    );
            }
        }

        return $this->render('@BenGorUser/sign_up/by_invitation.html.twig', [
            'email' => $user->getUsername(),
            'form'  => $form->createView(),
        ]);
    }
}
