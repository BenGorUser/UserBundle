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

use BenGor\User\Infrastructure\Persistence\Doctrine\Types\UserRolesType;
use BenGor\UserBundle\DependencyInjection\Compiler\DoctrineCustomTypesCompilerPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of load doctrine custom types compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DoctrineCustomTypesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DoctrineCustomTypesCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('doctrine.dbal.connection_factory')->shouldBeCalled()->willReturn(true);
        $container->getParameter('doctrine.dbal.connection_factory.types')->shouldBeCalled()->willReturn([]);

        $container->setParameter('doctrine.dbal.connection_factory.types', [
            'user_roles' => [
                'class'     => UserRolesType::class,
                'commented' => true,
            ],
        ])->shouldBeCalled();

        $container->findDefinition('doctrine.dbal.connection_factory')->shouldBeCalled()->willReturn($definition);
        $definition->replaceArgument(0, '%doctrine.dbal.connection_factory.types%')
            ->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
