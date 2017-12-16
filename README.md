# linkorb/silex-provider-sentry

Provides a service which wraps Sentry's `Raven_Client`.

Consumers of the service may use the `sendException` method which immediately
sends the exception to the configured instance of Sentry.  There is also the
`queueException` method which queues exceptions for later sending after the
`KernelEvents::TERMINATE` event has fired.


## Install

Install using composer:-

    $ composer require linkorb/silex-provider-sentry

Then configure and register the provide:-

    // app/app.php
    use LinkORB\Sentry\Provider\SentryServiceProvider;
    ...
    $app->register(
        new SentryServiceProvider,
        [
            'sentry.dsn' => "...",
            'sentry.options' => [
                ...
            ],
        ]
    );
