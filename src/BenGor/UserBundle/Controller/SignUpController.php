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

use BenGor\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGor\User\Domain\Model\Exception\UserGuestDoesNotExistException;
use BenGor\User\Domain\Model\UserGuest;
use BenGor\User\Domain\Model\UserToken;
use BenGor\UserBundle\Form\Type\SignUpByInvitationType;
use BenGor\UserBundle\Form\Type\SignUpType;
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
                    $this->addFlash('notice', 'Your changes were saved!');

                    return $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', 'The email is already in use.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
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
        $userGuest = $this->get('bengor_user.' . $userClass . '_guest_repository')
            ->userGuestOfInvitationToken(new UserToken($invitationToken));
        if (!$userGuest instanceof UserGuest) {
            throw $this->createNotFoundException('Invitation token does not exist');
        }

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
                    $this->addFlash('notice', 'Your changes were saved!');

                    return $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', 'The email is already in use.');
                } catch (UserGuestDoesNotExistException $exception) {
                    $this->addFlash('error', 'Sorry, you are not invited yet.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/sign_up/by_invitation.html.twig', [
            'email' => $userGuest->email()->email(),
            'form'  => $form->createView(),
        ]);
    }
}
