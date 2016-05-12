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

use BenGorUser\User\Application\Service\SignUp\SignUpUserCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * By invitation sign up user form type.
 *
 * It is valid for "by_invitation" or "by_invitation_with_confirmation" specifications.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpByInvitationType extends AbstractType
{
    /**
     * Array which contains the default role|roles.
     *
     * @var array
     */
    protected $roles;

    /**
     * The invitation token.
     *
     * @var string
     */
    protected $token;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'sign_up.form_password_invalid_message',
                'first_options'   => ['label' => 'sign_up.form_password_first_option_label'],
                'second_options'  => ['label' => 'sign_up.form_password_second_option_label'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'sign_up.form_submit_button',
            ]);

        $this->roles = $options['roles'];
        $this->token = $options['invitation_token'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['roles', 'invitation_token']);
        $resolver->setDefaults([
            'data_class' => SignUpUserCommand::class,
            'empty_data' => function (FormInterface $form) {
                return SignUpUserCommand::fromInvitationToken(
                    $this->token,
                    $form->get('password')->getData(),
                    $this->roles
                );
            },
        ]);
    }
}