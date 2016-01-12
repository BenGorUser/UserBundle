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

namespace BenGor\UserBundle\Form\Type;

use BenGor\User\Application\Service\SignUpUserByInvitationRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Registration by invitation form type.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RegistrationByInvitationType extends AbstractType
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
                'invalid_message' => 'The password fields must match.',
                'first_options'   => ['label' => 'Password'],
                'second_options'  => ['label' => 'Repeat Password'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register',
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
            'data_class' => SignUpUserByInvitationRequest::class,
            'empty_data' => function (FormInterface $form) {
                return new SignUpUserByInvitationRequest(
                    $this->token,
                    $form->get('password')->getData(),
                    $this->roles
                );
            },

        ]);
    }
}
