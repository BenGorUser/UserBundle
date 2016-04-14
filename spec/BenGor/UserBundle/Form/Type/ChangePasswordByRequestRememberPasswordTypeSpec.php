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

use BenGor\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Spec file of ChangePasswordByRequestRememberPasswordType class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordByRequestRememberPasswordTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ChangePasswordByRequestRememberPasswordType::class);
    }

    function it_extends_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('newPlainPassword', RepeatedType::class, [
            'type'            => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'first_options'   => ['label' => 'Password'],
            'second_options'  => ['label' => 'Repeat Password'],
        ])->shouldBeCalled()->willReturn($builder);
        $builder->add('submit', SubmitType::class, [
            'label' => 'Change password',
        ])->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, ['remember_password_token' => 'the-remember_password_token']);
    }

    function it_configures_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::type('array'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setRequired('remember_password_token')->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver);
    }
}
