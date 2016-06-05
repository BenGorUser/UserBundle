<?php

/*
 * This file is part of the BenGorUser package.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGorUser\UserBundle\EventBus;

use BenGorUser\User\Domain\Model\Event\UserEvent;
use BenGorUser\User\Infrastructure\Domain\Model\UserEventBus;
use SimpleBus\Message\Bus\MessageBus;

/**
 * Simple bus implementation of user event bus.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SimpleBusUserEventBus implements UserEventBus
{
    /**
     * The message bus.
     *
     * @var MessageBus
     */
    private $messageBus;

    /**
     * Constructor.
     *
     * @param MessageBus $aMessageBus The message bus
     */
    public function __construct(MessageBus $aMessageBus)
    {
        $this->messageBus = $aMessageBus;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UserEvent $anEvent)
    {
        $this->messageBus->handle($anEvent);
    }
}
