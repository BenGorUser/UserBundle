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

namespace BenGorUser\UserBundle\Controller;

use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\Exception\UserGuestDoesNotExistException;
use BenGorUser\User\Domain\Model\UserGuest;
use BenGorUser\User\Domain\Model\UserToken;
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request, $userClass, $firewall)
    {
        $form = $this->createForm(SignUpType::class, null, [
            'roles' => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->get('bengor_user.sign_up_' . $userClass);
                try {
                    $user = $service->execute($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('sign_up.success_flash'));

                    return $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('sign_up.error_flash_user_already_exist'));
                } catch (\Exception $exception) {
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function byInvitationAction(Request $request, $invitationToken, $userClass, $firewall)
    {
        $userGuest = $this->getUserGuestByToken($userClass, $invitationToken);
        $form = $this->createForm(SignUpByInvitationType::class, null, [
            'roles'            => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
            'invitation_token' => $invitationToken,
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->get('bengor_user.sign_up_' . $userClass);
                try {
                    $user = $service->execute($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('sign_up.success_flash'));

                    return $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('sign_up.error_flash_user_already_exist'));
                } catch (UserGuestDoesNotExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('sign_up.error_flash_user_guest_does_not_exist'));
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('sign_up.error_flash_generic'));
                }
            }
        }

        return $this->render('@BenGorUser/sign_up/by_invitation.html.twig', [
            'email' => $userGuest->email()->email(),
            'form'  => $form->createView(),
        ]);
    }

    /**
     * This extra query is a trade off related with the flow of application service.
     *
     * In "GET" requests we need to know if the invitation
     * token given exists in database, in case that
     * it isn't, it throws 404.
     *
     * @param string $userClass       The user type
     * @param string $invitationToken The invitation token
     *
     * @return UserGuest
     */
    private function getUserGuestByToken($userClass, $invitationToken)
    {
        $userGuest = $this->get('bengor_user.' . $userClass . '_guest_repository')->userGuestOfInvitationToken(
            new UserToken($invitationToken)
        );
        if (!$userGuest instanceof UserGuest) {
            throw $this->createNotFoundException();
        }

        return $userGuest;
    }
}
