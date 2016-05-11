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

namespace spec\BenGorUser\UserBundle\Form\Type;

use BenGorUser\UserBundle\Form\Type\InviteType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Spec file of invite type class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class InviteTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(InviteType::class);
    }

    function it_extends_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('email', EmailType::class)->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_configures_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::type('array'))->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver);
    }
}
