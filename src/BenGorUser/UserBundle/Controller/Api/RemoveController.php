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

use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\UserBundle\Form\FormErrorSerializer;
use BenGorUser\UserBundle\Form\Type\RemoveType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Remove user controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RemoveController extends Controller
{
    /**
     * Remove ser password action.
     *
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeAction(Request $request, $userClass)
    {
        $form = $this->get('form.factory')->createNamedBuilder('', RemoveType::class, null, [
            'csrf_protection' => false,
        ])->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $this->get('bengor_user.' . $userClass . '.api_command_bus')->handle(
                    $form->getData()
                );

                return new JsonResponse();
            } catch (UserDoesNotExistException $exception) {
                return new JsonResponse(
                    sprintf(
                        'The "%s" user id does not exist',
                        $form->getData()->id()
                    ),
                    400
                );
            }
        }

        return new JsonResponse(FormErrorSerializer::errors($form), 400);
    }
}
