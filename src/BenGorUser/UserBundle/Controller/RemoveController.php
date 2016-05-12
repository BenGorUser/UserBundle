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

use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\UserBundle\Form\Type\RemoveType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request, $userClass, $successRoute)
    {
        $form = $this->createForm(RemoveType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor.user.command_bus.' . $userClass)->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('remove.success_flash'));

                    return $this->redirectToRoute($successRoute);
                } catch (UserDoesNotExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('remove.error_flash_user_does_not_exist'));
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('remove.error_flash_generic'));
                }
            }
        }

        return $this->render('@BenGorUser/remove/remove.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
