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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * By request remember user password form type.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordByRequestRememberPasswordType extends AbstractType
{
    /**
     * The remember password token.
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
            ->add('newPlainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'change_password.form_password_invalid_message',
                'first_options'   => ['label' => 'change_password.form_password_first_option_label'],
                'second_options'  => ['label' => 'change_password.form_password_second_option_label'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'change_password.form_submit_button',
            ]);

        $this->token = $options['remember_password_token'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('remember_password_token');
        $resolver->setDefaults([
            'data_class' => ChangeUserPasswordCommand::class,
            'empty_data' => function (FormInterface $form) {
                return ChangeUserPasswordCommand::fromRememberPasswordToken(
                    $form->get('newPlainPassword')->getData(),
                    $this->token
                );
            },
        ]);
    }
}
