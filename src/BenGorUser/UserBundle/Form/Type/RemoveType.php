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

namespace BenGorUser\UserBundle\Form\Type;

use BenGorUser\User\Application\Service\Remove\RemoveUserCommand;
use BenGorUser\UserBundle\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Remove user form type.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RemoveType extends AbstractType
{
    /**
     * The current logged user.
     *
     * @var User|null
     */
    private $currentUser;

    /**
     * Constructor.
     *
     * @param TokenStorageInterface $aTokenStorage The token storage
     */
    public function __construct(TokenStorageInterface $aTokenStorage)
    {
        if ($aTokenStorage->getToken() instanceof TokenInterface) {
            $this->currentUser = $aTokenStorage->getToken()->getUser();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RemoveUserCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new RemoveUserCommand(
                    $this->currentUser->id()->id()
                );
            },
        ]);
    }
}
