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

namespace BenGor\UserBundle\DependencyInjection\Compiler;

use BenGor\User\Domain\Event\UserInvitedMailerSubscriber;
use BenGor\User\Domain\Event\UserRegisteredMailerSubscriber;
use BenGor\User\Domain\Model\Event\UserInvited;
use BenGor\User\Domain\Model\Event\UserRegistered;
use BenGor\User\Domain\Model\Event\UserRememberPasswordRequested;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Loads subscribers into event domain publisher compiler pass.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class LoadSubscribersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->loadImplementedSubscribers($container);

//        $definition = $container->findDefinition(
//            'bengor.user_bundle.event_listener.domain_event_publisher'
//        );
//
//        $taggedServices = $container->findTaggedServiceIds('bengor_user_subscriber');
//
//        $references = [];
//        foreach ($taggedServices as $id => $tags) {
//            $references[] = new Reference($id);
//        }
//        $definition->replaceArgument(0, $references);
    }

    /**
     * Loads default subscribers assigned into config.yml.
     *
     * @param ContainerBuilder $container The container builder
     */
    private function loadImplementedSubscribers(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');
        foreach ($config['subscribers'] as $key => $mailer) {
            if ('invited_mailer' === $key) {
                $this->buildSubscriber(
                    $container, $key, UserInvitedMailerSubscriber::class, $mailer, UserInvited::class
                );
            } elseif ('registered_mailer' === $key) {
                $this->buildSubscriber(
                    $container, $key, UserRegisteredMailerSubscriber::class, $mailer, UserRegistered::class
                );
            } elseif ('remember_password_requested' === $key) {
                $this->buildSubscriber(
                    $container, $key, UserRememberPasswordRequested::class, $mailer, UserRememberPasswordRequested::class
                );
            }
        }
    }

    /**
     * Builds a subscriber service with the given parameters.
     *
     * @param ContainerBuilder $container   The container builder
     * @param string           $name        The subscriber name
     * @param string           $subscriber  The subscriber fully qualified namespace
     * @param string           $mailer      The type of mailer
     * @param string           $domainEvent The domain event fully qualified namespace
     */
    private function buildSubscriber(ContainerBuilder $container, $name, $subscriber, $mailer, $domainEvent)
    {
        $container->setDefinition(
            'bengor.user.domain.event.user_' . $name . '_subscriber',
            new Definition(
                $subscriber, [
                    $container->getDefinition(
                        'bengor.user.infrastructure.mailing.' . $mailer . '.user_mailer'
                    ),
                    $container->getParameter('mailer_user'),
                    'Body test content',
                ]
            )
        )->addTag('bengor_user_subscriber', [
            'subscribes_to' => $domainEvent,
        ])->setPublic(false);
    }
}
