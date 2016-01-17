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
use BenGor\UserBundle\DependencyInjection\Compiler\DefaultRolesCompilerPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Spec file of load default roles compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DefaultRolesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultRolesCompilerPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes(ContainerBuilder $container)
    {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class' => User::class, 'default_roles' => [
                        'ROLE_USER',
                    ],
                ],
            ],
        ]);

        $container->setParameter('bengor_user.user_default_roles', ['ROLE_USER'])
            ->shouldBeCalled()->willReturn($container);

        $this->process($container);
    }

    function it_does_not_process_because_default_role_does_not_available(ContainerBuilder $container)
    {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class' => User::class, 'default_roles' => [
                        'ROLE_USER', 'ROLE_UNAVAILABLE',
                    ],
                ],
            ],
        ]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringProcess($container);
    }
}
