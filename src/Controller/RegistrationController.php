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
use BenGor\UserBundle\Form\Type\RegistrationType;
use Ddd\Application\Service\TransactionalApplicationService;
use Ddd\Infrastructure\Application\Service\DoctrineSession;
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
     * @param Request $request   The request
     * @param string  $userClass Extra parameter that contains the user type
     * @param string  $firewall  Extra parameter that contains the firewall name
     * @param string  $pattern   Extra parameter that contains the pattern success url
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, $userClass, $firewall, $pattern)
    {
        $form = $this->createForm(RegistrationType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = new TransactionalApplicationService(
                    $this->get('bengor.user.application.service.sign_up_' . $userClass),
                    new DoctrineSession($this->get('doctrine')->getManager())
                );

                try {
                    $response = $service->execute($form->getData());

                    $this
                        ->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $response->user(),
                            $request,
                            $this->get('bengor.user_bundle.security.form_login_' . $userClass . '_authenticator'),
                            $firewall
                        );
                    $this->addFlash('notice', 'Your changes were saved!');

                    return $this->redirectToRoute($pattern . 'homepage');
                } catch (UserAlreadyExistException $exception) {
                    $this->addFlash('error', 'The email is already in use.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'An error occurred. Please contact with the administrator.');
                }
            }
        }

        return $this->render('@BenGorUser/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
