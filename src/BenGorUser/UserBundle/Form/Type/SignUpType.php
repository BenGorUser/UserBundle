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

use BenGorUser\User\Application\Service\SignUp\SignUpUserCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Sign up user form type.
 *
 * It is valid for "default" or "with_confirmation" specifications.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpType extends AbstractType
{
    /**
     * Array which contains the default role|roles.
     *
     * @var array
     */
    protected $roles;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
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
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['roles']);
        $resolver->setDefaults([
            'data_class' => SignUpUserCommand::class,
            'empty_data' => function (FormInterface $form) {
                return SignUpUserCommand::fromEmail(
                    $form->get('email')->getData(),
                    $form->get('password')->getData(),
                    $this->roles
                );
            },

        ]);
    }
}
