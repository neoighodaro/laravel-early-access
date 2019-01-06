<?php

namespace Neo\EarlyAccess\SubscriptionServices;

use Neo\EarlyAccess\Subscriber;
use Neo\EarlyAccess\Contracts\Subscription\SubscriptionProvider;
use Neo\EarlyAccess\Contracts\Subscription\SubscriptionRepository;

class DatabaseService implements SubscriptionProvider
{
    /**
     * @var \Neo\EarlyAccess\SubscriptionServices\DatabaseRepository\SubscriptionRepository
     */
    protected $repository;

    /**
     * DatabaseSubscriptionService constructor.
     *
     * @param \Neo\EarlyAccess\Contracts\Subscription\SubscriptionRepository $repository
     */
    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Adds a new email to the subscribers list.
     *
     * @param string $email
     * @param string|null $name
     * @return bool
     */
    public function add(string $email, string $name = null): bool
    {
        return (bool) $this->repository->addSubscriber($email, $name);
    }

    /**
     * Removes an email from the subscribers list.
     *
     * @param string $email
     * @return bool
     */
    public function remove(string $email): bool
    {
        return $this->repository->removeSubscriber($email);
    }

    /**
     * Verifies a subscribers email address.
     *
     * @param string $email
     * @return bool
     */
    public function verify(string $email): bool
    {
        return $this->repository->verify($email);
    }

    /**
     * Find a subscriber using their email address.
     *
     * @param string $email
     * @return \Neo\EarlyAccess\Subscriber|false
     */
    public function findByEmail(string $email)
    {
        return with($this->repository->findByEmail($email), function ($user) {
            return $user ? new Subscriber($user) : false;
        });
    }
}
