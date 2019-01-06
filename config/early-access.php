<?php

return [

    /*
     * Is early access active? When active, the package will display the early access page request for all routes.
     */
    'enabled' => env('EARLY_ACCESS_ENABLED', false),

    /*
     * The URL to use as the early access page
     */
    'url' => env('EARLY_ACCESS_URL', '/early-access'),

    /*
     * URL to your applications login page.
     */
    'login_url' => env('EARLY_ACCESS_LOGIN_URL', '/login'),

    /*
     * Twitter handle without the @. This will be added to the share message included with the subscription message.
     */
    'twitter_handle' => env('EARLY_ACCESS_TWITTER_HANDLE'),

    /*
     * The early access view to load.
     */
    'view' => env('EARLY_ACCESS_VIEW', 'early-access::index'),

    /*
     * Service driver to use.
     *
     * Supported: database
     *
     * To add your own driver, create a class that implements the `Neo\EarlyAccess\Contracts\Subscription\SubscriptionProvider`
     * contract. Register the class in your `AppServiceProvider` making sure to prepend the name with 'early-access.'.
     * Example:
     *
     * $this->app->instance('early-access.service-name', function() {
     *      return new ServiceClass;
     * });
     *
     * To set your custom service, change the value below to your service name (without the 'early-access.').
     */
    'service' => env('EARLY_ACCESS_SERVICE_DRIVER', 'database'),

    'services' => [

        /*
         * Database settings...
         */
        'database' => [
            'table_name' => env('EARLY_ACCESS_SERVICE_DB_TABLE', 'subscribers'),
        ],

    ],

    /*
     * Notification classes to be called when a user completes an action.
     */
    'notifications' => [
        'subscribed' => Neo\EarlyAccess\Notifications\UserSubscribed::class,
        'unsubscribed' => Neo\EarlyAccess\Notifications\UserUnsubscribed::class,
    ],

];
