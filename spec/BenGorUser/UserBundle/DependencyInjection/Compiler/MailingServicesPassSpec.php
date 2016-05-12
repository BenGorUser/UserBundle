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

namespace spec\BenGorUser\UserBundle\DependencyInjection\Compiler;

use BenGorUser\User\Domain\Model\User;
use BenGorUser\UserBundle\DependencyInjection\Compiler\MailingServicesPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of mailing services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class MailingServicesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MailingServicesPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('swiftmailer.mailer.default')->shouldBeCalled()->willReturn(true);
        $container->getDefinition('swiftmailer.mailer.default')->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.infrastructure.mailing.mailer.swift_mailer',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $definition->setPublic(false)->shouldBeCalled()->willReturn($definition);

        $container->setAlias(
            'bengor_user.mailer.swift_mailer',
            'bengor.user.infrastructure.mailing.mailer.swift_mailer'
        )->shouldBeCalled();

        $this->process($container);
    }
}