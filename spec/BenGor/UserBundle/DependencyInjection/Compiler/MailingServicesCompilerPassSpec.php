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

namespace spec\BenGor\UserBundle\DependencyInjection\Compiler;

use BenGor\User\Domain\Model\User;
use BenGor\UserBundle\DependencyInjection\Compiler\MailingServicesCompilerPass;
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
class MailingServicesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MailingServicesCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container)
    {
        $container->hasDefinition('mailer')->shouldBeCalled()->willReturn(true);
        $container->getDefinition('mailer')->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.infrastructure.mailing.swift_mailer.user_mailer',
            Argument::type(Definition::class)
        )->shouldBeCalled();

        $this->process($container);
    }
}
