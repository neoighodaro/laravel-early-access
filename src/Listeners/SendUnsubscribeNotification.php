<?php

namespace Neo\EarlyAccess\Listeners;

use Neo\EarlyAccess\Events\UserUnsubscribed;

class SendUnsubscribeNotification
{
    /**
     * Handle the event.
     *
     * @param \Neo\EarlyAccess\Events\UserUnsubscribed $event
     * @return void
     */
    public function handle(UserUnsubscribed $event)
    {
        $notifierClass = config('early-access.notifications.unsubscribed');

        $event->subscriber->notify(
            new $notifierClass($event->subscriber)
        );
    }
}
