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

use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Domain\Model\Exception\UserInvitationAlreadyAcceptedException;
use BenGorUser\UserBundle\Form\Type\InviteType;
use BenGorUser\UserBundle\Form\Type\ResendInvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Invite user controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class InviteController extends Controller
{
    /**
     * Invite action.
     *
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name, by default is null
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction(Request $request, $userClass, $successRoute = null)
    {
        $form = $this->createForm(InviteType::class, null, [
            'roles' => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor_user.' . $userClass . '.command_bus')->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans(
                        'invite.success_flash', [], 'BenGorUser'
                    ));
                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans(
                        'invite.error_flash_user_already_exists', [], 'BenGorUser'
                    ));
                }
            }
        }

        return $this->render('@BenGorUser/invite/invite.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Invite action.
     *
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name, by default is null
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resendInvitationAction(Request $request, $userClass, $successRoute = null)
    {
        $form = $this->createForm(ResendInvitationType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor_user.' . $userClass . '.command_bus')->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans(
                        'resend_invitation.success_flash', [], 'BenGorUser'
                    ));
                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserDoesNotExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans(
                        'resend_invitation.error_flash_user_does_not_exist', [], 'BenGorUser'
                    ));
                } catch (UserInvitationAlreadyAcceptedException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans(
                        'resend_invitation.error_flash_user_invitation_already_accepted', [], 'BenGorUser'
                    ));
                }
            }
        }

        return $this->render('@BenGorUser/invite/resend_invitation.html.twig', ['form' => $form->createView()]);
    }
}
