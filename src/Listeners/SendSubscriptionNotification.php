<?php

namespace Neo\EarlyAccess\Listeners;

use Neo\EarlyAccess\Events\UserSubscribed;

class SendSubscriptionNotification
{
    /**
     * Handle the event.
     *
     * @param \Neo\EarlyAccess\Events\UserSubscribed $event
     * @return void
     */
    public function handle(UserSubscribed $event)
    {
        $notifierClass = config('early-access.notifications.subscribed');

        $event->subscriber->notify(
            new $notifierClass($event->subscriber)
        );
    }
}
