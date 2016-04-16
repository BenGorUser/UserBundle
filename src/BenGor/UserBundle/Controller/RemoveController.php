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

use BenGor\User\Domain\Model\Exception\UserPasswordInvalidException;
use BenGor\UserBundle\Form\Type\RemoveType;
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
                $service = $this->get('bengor_user.remove_' . $userClass);

                try {
                    $service->execute($form->getData());
                    $this->addFlash('notice', 'The account is successfully removed');

                    return $this->redirectToRoute($successRoute);
                } catch (UserPasswordInvalidException $exception) {
                    $this->addFlash('error', 'The current password is not correct');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/remove/remove.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
