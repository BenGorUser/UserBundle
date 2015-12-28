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
use BenGor\UserBundle\DependencyInjection\Compiler\ApplicationServicesCompilerPass;
use BenGor\UserBundle\DependencyInjection\Compiler\SecurityServicesCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Spec file of security services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SecurityServicesCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SecurityServicesCompilerPass::class);
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
                    'class' => User::class, 'firewall' => [
                        'name' => 'user', 'pattern' => '',
                    ],
                ],
            ],
        ]);

        $container->getDefinition('user_password_encoder')->shouldBeCalled();

        $container->setDefinition(
            'user_password_encoder',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.infrastructure.security.symfony.user_password_encoder',
            Argument::type(Definition::class)
        )->shouldBeCalled();

        $this->process($container);
    }
}
