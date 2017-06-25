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

use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Domain\Model\Exception\UserInvitationAlreadyAcceptedException;
use BenGorUser\UserBundle\Form\FormErrorSerializer;
use BenGorUser\UserBundle\Form\Type\InviteType;
use BenGorUser\UserBundle\Form\Type\ResendInvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function inviteAction(Request $request, $userClass)
    {
        $form = $this->get('form.factory')->createNamedBuilder('', InviteType::class, null, [
            'roles'           => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
            'csrf_protection' => false,
        ])->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $this->get('bengor_user.' . $userClass . '.api_command_bus')->handle(
                    $form->getData()
                );

                return new JsonResponse([
                    'user_id' => $form->getData()->id(),
                    'email'   => $form->getData()->email(),
                    'role'    => $form->getData()->roles(),
                ]);
            } catch (UserAlreadyExistException $exception) {
                return new JsonResponse(
                    sprintf(
                        'The %s email is already invited',
                        $form->getData()->email()
                    ),
                    409
                );
            }
        }

        return new JsonResponse(FormErrorSerializer::errors($form), 400);
    }

    /**
     * Invite action.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function resendInvitationAction(Request $request, $userClass)
    {
        $form = $this->get('form.factory')->createNamedBuilder('', ResendInvitationType::class, null, [
            'csrf_protection' => false,
        ])->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $this->get('bengor_user.' . $userClass . '.api_command_bus')->handle(
                    $form->getData()
                );

                return new JsonResponse([
                    'email' => $form->getData()->email(),
                ]);
            } catch (UserDoesNotExistException $exception) {
                return new JsonResponse(
                    sprintf(
                        'The %s email is does not exist',
                        $form->getData()->email()
                    ),
                    404
                );
            } catch (UserInvitationAlreadyAcceptedException $exception) {
                return new JsonResponse(
                    sprintf(
                        'The %s email is already accepted invitation',
                        $form->getData()->email()
                    ),
                    409
                );
            }
        }

        return new JsonResponse(FormErrorSerializer::errors($form), 400);
    }
}
