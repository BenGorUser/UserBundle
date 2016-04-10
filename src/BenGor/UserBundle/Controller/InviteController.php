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
use BenGor\UserBundle\Form\Type\InviteType;
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
     * @param string|null $successRoute Extra parameter that contains the success route name, by default is null
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction(Request $request, $userClass, $successRoute = null)
    {
        $form = $this->createForm(InviteType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->get('bengor_user.invite_' . $userClass);

                try {
                    $service->execute($form->getData());
                    $this->addFlash('notice', 'Invitation is successfully done');

                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', 'The email is already in use.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/invite/invite.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
