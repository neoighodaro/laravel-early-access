<?php

namespace Neo\EarlyAccess\Notifications;

use Illuminate\Bus\Queueable;
use Neo\EarlyAccess\Subscriber;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserUnsubscribed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \Neo\EarlyAccess\Subscriber
     */
    public $subscriber;

    /**
     * Create a new notification instance.
     *
     * @param  \Neo\EarlyAccess\Subscriber $subscriber
     */
    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return (new MailMessage)
            ->subject(trans('early-access::mail.unsubscribed.subject', ['name' => config('app.name')]))
            ->line(trans('early-access::mail.unsubscribed.message.intro'));
    }
}
