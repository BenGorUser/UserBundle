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

namespace BenGorUser\UserBundle\Form\Type;

use BenGorUser\User\Application\Service\ChangePassword\ChangeUserPasswordCommand;
use BenGorUser\UserBundle\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Change user password form type.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPlainPassword', PasswordType::class)
            ->add('newPlainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'change_password.form_password_invalid_message',
                'first_options'   => ['label' => 'change_password.form_password_first_option_label'],
                'second_options'  => ['label' => 'change_password.form_password_second_option_label'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'change_password.form_submit_button',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangeUserPasswordCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new ChangeUserPasswordCommand(
                    $this->currentUser->id()->id(),
                    $form->get('newPlainPassword')->getData(),
                    $form->get('oldPlainPassword')->getData()
                );
            },
        ]);
    }
}
