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
use BenGorUser\UserBundle\Form\Type\InviteType;
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
                try {
                    $this->get('bengor.user.command_bus.'.$userClass)->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('invite.success_flash'));

                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('invite.error_flash_user_already_exist'));
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('invite.error_flash_generic'));
                }
            }
        }

        return $this->render('@BenGorUser/invite/invite.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
