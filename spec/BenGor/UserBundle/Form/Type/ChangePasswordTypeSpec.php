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

namespace spec\BenGor\UserBundle\Form\Type;

use BenGor\UserBundle\Form\Type\ChangePasswordType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Spec file of ChangePasswordType class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordTypeSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage)
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChangePasswordType::class);
    }

    function it_extends_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('oldPlainPassword', PasswordType::class)
            ->shouldBeCalled()->willReturn($builder);
        $builder->add('newPlainPassword', RepeatedType::class, [
            'type'            => PasswordType::class,
            'invalid_message' => 'change_password.form_password_invalid_message',
            'first_options'   => ['label' => 'change_password.form_password_first_option_label'],
            'second_options'  => ['label' => 'change_password.form_password_second_option_label'],
        ])->shouldBeCalled()->willReturn($builder);
        $builder->add('submit', SubmitType::class, [
            'label' => 'change_password.form_submit_button',
        ])->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_configures_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::type('array'))->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver);
    }
}
