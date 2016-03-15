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
use BenGor\UserBundle\Form\Type\InvitationType;
use BenGor\UserBundle\Form\Type\RegistrationByInvitationType;
use BenGor\UserBundle\Form\Type\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Registration controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegistrationController extends Controller
{
    /**
     * Register action.
     *
     * @param Request $request      The request
     * @param string  $userClass    Extra parameter that contains the user type
     * @param string  $firewall     Extra parameter that contains the firewall name
     * @param string  $successRoute Extra parameter that contains the success route name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, $userClass, $firewall, $successRoute)
    {
        $form = $this->createForm(RegistrationType::class, null, [
            'roles' => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->get('bengor_user.sign_up_' . $userClass);

                try {
                    $response = $service->execute($form->getData());

                    $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $response->user(),
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                    $this->addFlash('notice', 'Your changes were saved!');

                    return $this->redirectToRoute($successRoute);
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', 'The email is already in use.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', $exception->getMessage());
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Invite action.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction(Request $request, $userClass)
    {
        $form = $this->createForm(InvitationType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->get('bengor_user.invite_' . $userClass);

                try {
                    $service->execute($form->getData());
                    $this->addFlash('notice', 'Invitation is successfully done');
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', 'The email is already in use.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/registration/invite.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Register by invitation action.
     *
     * @param Request $request         The request
     * @param string  $invitationToken The invitation token
     * @param string  $userClass       Extra parameter that contains the user type
     * @param string  $firewall        Extra parameter that contains the firewall name
     * @param string  $successRoute    Extra parameter that contains the success route name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerByInvitationAction(Request $request, $invitationToken, $userClass, $firewall, $successRoute)
    {
        $userGuest = $this->get('bengor_user.' . $userClass . '_guest_repository')
            ->userGuestOfInvitationToken(new UserToken($invitationToken));
        if (!$userGuest instanceof UserGuest) {
            throw $this->createNotFoundException('Invitation token does not exist');
        }

        $form = $this->createForm(RegistrationByInvitationType::class, null, [
            'roles'            => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
            'invitation_token' => $invitationToken,
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->get('bengor_user.sign_up_' . $userClass . '_by_invitation');

                try {
                    $response = $service->execute($form->getData());

                    $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $response->user(),
                            $request,
                            $this->get('bengor_user.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                    $this->addFlash('notice', 'Your changes were saved!');

                    return $this->redirectToRoute($successRoute);
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', 'The email is already in use.');
                } catch (UserGuestDoesNotExistException $exception) {
                    $this->addFlash('error', 'The guest email does not exist.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/registration/register_by_invitation.html.twig', [
            'email' => $userGuest->email()->email(),
            'form'  => $form->createView(),
        ]);
    }
}
