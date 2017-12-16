<?php

namespace LinkORB\Sentry;

use Raven_Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Store errors and exceptions in Sentry.
 */
class SentryService implements SentryInterface, EventSubscriberInterface
{
    /**
     * @var \Raven_Client
     */
    private $client;
    private $exceptions = [];

    /**
     * @param \Raven_Client $client
     */
    public function __construct(Raven_Client $client)
    {
        $this->client = $client;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'handlePostResponseEvent',
        ];
    }

    public function queueException($exception)
    {
        $this->exceptions[] = $exception;
    }

    public function sendException($exception)
    {
        $this->client->captureException($exception);
    }

    public function sendQueuedExceptions()
    {
        foreach ($this->exceptions as $exception) {
            $this->sendException($exception);
        }
    }

    public function handlePostResponseEvent(PostResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $this->sendQueuedExceptions();
    }
}
