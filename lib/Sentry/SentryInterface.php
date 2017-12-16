<?php

namespace LinkORB\Sentry;

/**
 * Interface for reporting exceptions to Sentry.
 */
interface SentryInterface
{
    /**
     * Queue an exception so that it can be later sent to Sentry.
     *
     * @param object $exception
     */
    public function queueException($exception);

    /**
     * Immediately send an exception to Sentry.
     *
     * @param object $exception
     */
    public function sendException($exception);

    /**
     * Immediately send queued exceptions to Sentry.
     *
     * @param object $exception
     */
    public function sendQueuedExceptions();
}
