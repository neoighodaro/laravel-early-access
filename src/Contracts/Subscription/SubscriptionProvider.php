<?php

namespace Neo\EarlyAccess\Contracts\Subscription;

interface SubscriptionProvider
{
    /**
     * Adds a new email to the subscribers list.
     *
     * @param string $email
     * @param string|null $name
     * @return bool
     */
    public function add(string $email, string $name = null): bool;

    /**
     * Removes an email from the subscribers list.
     *
     * @param string $email
     * @return bool
     */
    public function remove(string $email): bool;

    /**
     * Verifies a subscribers email address.
     *
     * @param string $email
     * @return bool
     */
    public function verify(string $email): bool;

    /**
     * Find a subscriber using their email address.
     *
     * @param string $email
     * @return \Neo\EarlyAccess\Subscriber|false
     */
    public function findByEmail(string $email);
}
