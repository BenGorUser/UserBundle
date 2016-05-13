<?php

namespace BenGorUser\UserBundle\EventListener;

use BenGorUser\User\Domain\Model\UserAggregateRoot;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;

class CollectsEventsFromEntities implements EventSubscriber, ContainsRecordedMessages
{
    private $collectedEvents = array();

    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        );
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $this->collectEventsFromEntity($event);
    }

    public function recordedMessages()
    {
        return $this->collectedEvents;
    }

    public function eraseMessages()
    {
        $this->collectedEvents = array();
    }

    private function collectEventsFromEntity(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof UserAggregateRoot) {
            foreach ($entity->events() as $event) {
                $this->collectedEvents[] = $event;
            }

            $entity->eraseEvents();
        }
    }
}
