<?php

namespace Neo\EarlyAccess;

use Illuminate\Notifications\Notifiable;
use Neo\EarlyAccess\Events\UserSubscribed;
use Illuminate\Contracts\Support\Arrayable;
use Neo\EarlyAccess\Events\UserUnsubscribed;
use Neo\EarlyAccess\Contracts\Subscription\SubscriptionProvider;

/**
 * @property string|null email
 * @property string|null name
 * @property string|null subscribed_at
 * @property bool verified
 */
class Subscriber implements Arrayable
{
    use Notifiable;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var bool
     */
    protected $exists = false;

    /**
     * @var \Neo\EarlyAccess\Contracts\Subscription\SubscriptionProvider
     */
    protected $subscriber;

    /**
     * Subscriber constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->subscriber = app(SubscriptionProvider::class);

        $this->attributes = [
            'name' => $attributes['name'] ?? null,
            'email' => $attributes['email'] ?? null,
            'subscribed_at' => $attributes['subscribed_at'] ?? null,
            'verified' => (bool) ($attributes['verified'] ?? false),
        ];
    }

    /**
     * Shortcut for instantiating the object.
     *
     * @param array $attributes
     * @return \Neo\EarlyAccess\Subscriber
     */
    public static function make(array $attributes = [])
    {
        return new static($attributes);
    }

    /**
     * Subscribe the user.
     *
     * @return bool
     */
    public function subscribe(): bool
    {
        if ($this->subscribed()) {
            return true;
        }

        $subscribed = $this->subscriber->add($this->email, $this->name);

        $this->exists = $subscribed;

        $this->subscribed_at = (string) now();

        event(new UserSubscribed($this));

        return $subscribed;
    }

    /**
     * Unsubscribe the user.
     *
     * @return bool
     */
    public function unsubscribe(): bool
    {
        $unsubscribed = $this->subscriber->remove($this->email);

        if ($this->exists && $unsubscribed) {
            event(new UserUnsubscribed($this));
        }

        $this->exists = $unsubscribed === false;

        $this->subscribed_at = null;

        return $unsubscribed;
    }

    /**
     * Checks if user is subscribed.
     *
     * @return bool
     */
    public function subscribed(): bool
    {
        return $this->exists;
    }

    /**
     * Set the subscription status.
     *
     * @param bool $subscribed
     * @return $this
     */
    public function setSubscribed(bool $subscribed = true)
    {
        $this->exists = $subscribed;

        return $this;
    }

    /**
     * Find a user by email.
     *
     * @param string|null $email
     * @return \Neo\EarlyAccess\Subscriber|null
     */
    public function findByEmail(string $email = null): ?self
    {
        if (! $email and ! $this->email) {
            return null;
        }

        if ($this->subscribed() and ! $email) {
            return $this;
        }

        $subscriber = $this->subscriber->findByEmail($email ?? $this->email);

        return $subscriber ? $subscriber->setSubscribed() : null;
    }

    /**
     * Verify the subscriber.
     *
     * @return bool
     */
    public function verify()
    {
        $this->verified = $this->subscriber->verify($this->email);

        if (! $this->exists and $this->verified) {
            $this->exists = true;
        }

        return $this->verified;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->email;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->attributes[$name] ?? false) {
            return $this->attributes[$name];
        }
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] = $value;
        }
    }
}
