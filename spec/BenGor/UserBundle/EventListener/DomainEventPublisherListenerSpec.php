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

namespace spec\BenGor\UserBundle\EventListener;

use BenGor\UserBundle\EventListener\DomainEventPublisherListener;
use Ddd\Domain\DomainEventSubscriber;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Spec file of domain event publisher listener class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DomainEventPublisherListenerSpec extends ObjectBehavior
{
    function let(DomainEventSubscriber $subscriber)
    {
        $this->beConstructedWith([$subscriber]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DomainEventPublisherListener::class);
    }

    function it_listens_on_kernel_request(GetResponseEvent $event)
    {
        $this->onKernelRequest($event);
    }
}
