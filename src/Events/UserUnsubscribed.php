<?php

namespace Neo\EarlyAccess\Events;

use Neo\EarlyAccess\Subscriber;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserUnsubscribed implements ShouldQueue
{
    use SerializesModels;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'early-access';

    /**
     * Subscriber instance.
     *
     * @var \Neo\EarlyAccess\Subscriber
     */
    public $subscriber;

    /**
     * Create a new event instance.
     *
     * @param \Neo\EarlyAccess\Subscriber $subscriber
     */
    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }
}
