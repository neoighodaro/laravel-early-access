<?php

namespace Neo\EarlyAccess\Contracts\Subscription;

interface SubscriptionRepository
{
    /**
     * Adds a new subscriber to the repository.
     *
     * @param  string $email
     * @param  string|null $name
     * @return bool
     */
    public function addSubscriber(string $email, ?string $name): bool;

    /**
     * Removes a subscriber.
     *
     * @param string $email
     * @return bool
     */
    public function removeSubscriber(string $email): bool;

    /**
     * Verify the subscriber.
     *
     * @param  string $email
     * @return bool
     */
    public function verify(string $email): bool;

    /**
     * Find subscriber by email.
     *
     * @param  string $email
     * @return array|false
     */
    public function findByEmail(string $email);

}
