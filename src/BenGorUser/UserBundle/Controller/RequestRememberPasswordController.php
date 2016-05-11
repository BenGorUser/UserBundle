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

use BenGorUser\User\Domain\Model\Exception\UserDoesNotExistException;
use BenGorUser\UserBundle\Form\Type\RequestRememberPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request remember password controller.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RequestRememberPasswordController extends Controller
{
    /**
     * Request remember user password action.
     *
     * @param Request     $request      The request
     * @param string      $userClass    Extra parameter that contains the user type
     * @param string|null $successRoute Extra parameter that contains the success route name, by default is null
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestRememberPasswordAction(Request $request, $userClass, $successRoute = null)
    {
        $form = $this->createForm(RequestRememberPasswordType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('bengor.user.command_bus.'.$userClass)->handle($form->getData());
                    $this->addFlash('notice', $this->get('translator')->trans('request_remember_password.success_flash'));

                    if (null !== $successRoute) {
                        return $this->redirectToRoute($successRoute);
                    }
                } catch (UserDoesNotExistException $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('request_remember_password.error_flash_user_does_not_exist'));
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->get('translator')->trans('request_remember_password.error_flash_generic'));
                }
            }
        }

        return $this->render('@BenGorUser/request_remember_password/request_remember_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
