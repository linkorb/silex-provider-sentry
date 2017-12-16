<?php

namespace LinkORB\Sentry\Provider;

use RuntimeException;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Raven_Client;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

use LinkORB\Sentry\SentryService;

/**
 * Provides the "sentry.service" service.
 *
 * The Sentry client requires the Data Source Name (DSN) to be provided by
 * setting the "sentry.dsn" service parameter.
 *
 * Set the "sentry.auto_capture" service parameter to false to prevent the
 * automatic capture of unhandled errors and exceptions.
 *
 * Supply options for the Sentry client in the "sentry.options" service parameter.
 */
class SentryServiceProvider implements
    ServiceProviderInterface,
    BootableProviderInterface,
    EventListenerProviderInterface
{
    public function register(Container $app)
    {
        $app['sentry.auto_capture'] = true;
        $app['sentry.options'] = [];

        $app['sentry.service'] = function ($app) {
            if (!isset($app['sentry.dsn'])) {
                throw new RuntimeException(
                    'You must set the "sentry.dsn" container parameter in order to use the SentryServiceProvider.'
                );
            }
            return new SentryService(
                new Raven_Client($app['sentry.dsn'], $app['sentry.options'])
            );
        };
    }

    /**
     * Add an early error handler which queues the exception with SentryService
     * for sending to sentry post-request.
     *
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
        if (!$app['sentry.auto_capture']) {
            return;
        }
        $app->error(
            function ($e, Request $request, $code) use ($app) {
                $app['sentry.service']->queueException($e);
            },
            255
        );
    }

    /**
     * Subscribe SentryService.
     *
     * {@inheritdoc}
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['sentry.service']);
    }
}
