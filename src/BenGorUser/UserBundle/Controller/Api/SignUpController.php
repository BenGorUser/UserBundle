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

use BenGorUser\User\Application\Command\LogIn\LogInUserCommand;
use BenGorUser\User\Application\Query\UserOfInvitationTokenQuery;
use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\User\Domain\Model\Exception\UserInactiveException;
use BenGorUser\User\Domain\Model\Exception\UserPasswordInvalidException;
use BenGorUser\User\Domain\Model\Exception\UserTokenExpiredException;
use BenGorUser\UserBundle\Form\FormErrorSerializer;
use BenGorUser\UserBundle\Form\Type\SignUpByInvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param string  $formType  Extra parameter that contains the form type FQCN
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function defaultAction(Request $request, $userClass, $formType)
    {
        $roles = $this->getParameter('bengor_user.' . $userClass . '_default_roles');
        $form = $this->get('form.factory')->createNamedBuilder('', $formType, null, [
            'roles'           => $roles,
            'csrf_protection' => false,
        ])->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $commandBus = $this->get('bengor_user.' . $userClass . '.api_command_bus');
            $email = $form->getData()->email();

            try {
                $commandBus->handle(
                    $form->getData()
                );

                $commandBus->handle(
                    new LogInUserCommand(
                        $email,
                        $form->getData()->password()
                    )
                );
            } catch (UserDoesNotExistException $exception) {
                return new JsonResponse(null, 404);
            } catch (UserPasswordInvalidException $exception) {
                return new JsonResponse('Bad credentials', 400);
            } catch (UserAlreadyExistException $exception) {
                return new JsonResponse('Bad credentials', 400);
            } catch (UserInactiveException $exception) {
            }
            $token = $this->get('lexik_jwt_authentication.encoder.default')->encode(['email' => $email]);

            return new JsonResponse(['token' => $token]);
        }

        return new JsonResponse(FormErrorSerializer::errors($form), 400);
    }

    /**
     * By invitation action, that it can executes the "by_invitation" specification.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function byInvitationAction(Request $request, $userClass)
    {
        $invitationToken = $request->query->get('token');
        try {
            // we need to know if the invitation token given exists in
            // database, in case that it isn't, it throws 404.
            $user = $this->get('bengor_user.' . $userClass . '.by_invitation_token_query')->__invoke(
                new UserOfInvitationTokenQuery($invitationToken)
            );
        } catch (UserDoesNotExistException $exception) {
            return new JsonResponse(null, 404);
        } catch (UserTokenExpiredException $exception) {
            return new JsonResponse(null, 404);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(null, 404);
        }

        $form = $this->get('form.factory')->createNamedBuilder('', SignUpByInvitationType::class, null, [
            'roles'            => $this->getParameter('bengor_user.' . $userClass . '_default_roles'),
            'invitation_token' => $invitationToken,
            'csrf_protection'  => false,
        ])->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $commandBus = $this->get('bengor_user.' . $userClass . '.api_command_bus');

            try {
                $commandBus->handle(
                    $form->getData()
                );
                $commandBus->handle(
                    new LogInUserCommand(
                        $user['email'],
                        $form->getData()->password()
                    )
                );
            } catch (UserDoesNotExistException $exception) {
                return new JsonResponse(null, 404);
            } catch (UserInactiveException $exception) {
                return new JsonResponse(null, 404);
            } catch (UserPasswordInvalidException $exception) {
                return new JsonResponse('Bad credentials', 400);
            } catch (UserAlreadyExistException $exception) {
                return new JsonResponse('Bad credentials', 400);
            }
            $token = $this->get('lexik_jwt_authentication.encoder.default')->encode(['email' => $user['email']]);

            return new JsonResponse(['token' => $token]);
        }

        return new JsonResponse(FormErrorSerializer::errors($form), 400);
    }
}
