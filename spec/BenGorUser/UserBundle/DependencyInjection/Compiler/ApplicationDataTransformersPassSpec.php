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

namespace spec\BenGorUser\UserBundle\DependencyInjection\Compiler;

use BenGorUser\User\Domain\Model\User;
use BenGorUser\UserBundle\DependencyInjection\Compiler\ApplicationDataTransformersPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of ApplicationDataTransformersPass pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationDataTransformersPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationDataTransformersPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->setDefinition(
            'bengor.user.application.data_transformer.user_dto',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);
        $container->setDefinition(
            'bengor.user.application.data_transformer.user_no_transformation',
            Argument::type(Definition::class)
        )->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
