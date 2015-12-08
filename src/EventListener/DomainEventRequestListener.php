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

namespace BenGor\UserBundle\EventListener;

use Ddd\Domain\DomainEventPublisher;
use Ddd\Domain\DomainEventSubscriber;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Domain event request listener class.
 * It loads subscribers into domain event publisher.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
final class DomainEventRequestListener
{
    /**
     * Array which contains subscribers.
     *
     * @var array
     */
    private $subscribers;

    /**
     * Constructor.
     *
     * @param array $subscribers Array which contains subscribers
     */
    public function __construct(array $subscribers = [])
    {
        $this->subscribers = array_map(function ($subscriber) {
            if (!$subscriber instanceof DomainEventSubscriber) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The %s subscriber must be an instance of DomainEventSubscriber', $subscriber
                    )
                );
            }

            return $subscriber;
        }, $subscribers);
    }

    /**
     * Callback that executes on kernel request
     * loading the subscribers into domain event publisher.
     *
     * @param GetResponseEvent $event The given event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $eventPublisher = DomainEventPublisher::instance();
        foreach ($this->subscribers as $subscriber) {
            $eventPublisher->subscribe($subscriber);
        }
    }
}
