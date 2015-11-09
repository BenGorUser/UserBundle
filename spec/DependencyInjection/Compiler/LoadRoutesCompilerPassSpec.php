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

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of load routes compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class LoadRoutesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('BenGor\UserBundle\DependencyInjection\Compiler\LoadRoutesCompilerPass');
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('bengor.user_bundle.routing.security_routes_loader')
            ->shouldBeCalled()->willReturn(true);
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class' => 'BenGor\Domain\Model\User', 'firewall' => [
                        'name' => 'user', 'pattern' => '',
                    ],
                ],
            ],
        ]);
        $container->getDefinition('bengor.user_bundle.routing.security_routes_loader')
            ->shouldBeCalled()->willReturn($definition);
        $definition->replaceArgument(0, ['' => ''])->shouldBeCalled()->willReturn($definition);

        $this->process($container);
    }
}
