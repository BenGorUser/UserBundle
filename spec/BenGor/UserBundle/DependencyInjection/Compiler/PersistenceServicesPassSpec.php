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
use BenGor\UserBundle\DependencyInjection\Compiler\PersistenceServicesPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Spec file of persistence services compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class PersistenceServicesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PersistenceServicesPass::class);
    }

    function it_implmements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_does_not_process_because_doctrine_bundle_is_not_register_and_the_persistence_layer_is_doctrine(
        ContainerBuilder $container
    ) {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user' => [
                    'class'       => 'BenGor\User\Domain\Model\User',
                    'persistence' => 'doctrine',
                    'firewall'    => 'main',
                ],
            ],
        ]);

        $container->hasDefinition('doctrine.orm.default_entity_manager')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow(new RuntimeException(
            'When the persistence layer is "doctrine" requires ' .
            'the installation and set up of the DoctrineBundle'
        ))->duringProcess($container);
    }

    function it_processes(ContainerBuilder $container)
    {
        $container->getParameter('bengor_user.config')->shouldBeCalled()->willReturn([
            'user_class' => [
                'user'      => [
                    'class'       => 'BenGor\User\Domain\Model\User',
                    'persistence' => 'doctrine',
                    'firewall'    => 'main',
                ],
                'applicant' => [
                    'class'       => 'BenGor\User\Domain\Model\User',
                    'persistence' => 'sql',
                    'firewall'    => 'main',
                ],
            ],
        ]);

        $container->hasDefinition('doctrine.orm.default_entity_manager')->shouldBeCalled()->willReturn(true);

        $container->setDefinition(
            'bengor.user.infrastructure.persistence.user_repository',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.infrastructure.persistence.user_guest_repository',
            Argument::type(Definition::class)
        )->shouldBeCalled();

        $container->getParameter('database_name')->shouldBeCalled()->willReturn('dbname');
        $container->getParameter('database_user')->shouldBeCalled()->willReturn('dbuser');
        $container->getParameter('database_password')->shouldBeCalled()->willReturn('dbpass');
        $container->setDefinition(
            'pdo',
            Argument::type(Definition::class)
        )->shouldBeCalled();

        $container->setDefinition(
            'bengor.user.infrastructure.persistence.applicant_repository',
            Argument::type(Definition::class)
        )->shouldBeCalled();
        $container->setDefinition(
            'bengor.user.infrastructure.persistence.applicant_guest_repository',
            Argument::type(Definition::class)
        )->shouldBeCalled();

        $this->process($container);
    }
}
