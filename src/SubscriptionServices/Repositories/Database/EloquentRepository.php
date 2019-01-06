<?php

namespace Neo\EarlyAccess\SubscriptionServices\Repositories\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Neo\EarlyAccess\Contracts\Subscription\SubscriptionRepository;

class EloquentRepository extends Model implements SubscriptionRepository
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $dates = [
        'verified_at',
        'subscribed_at',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * EloquentRepository constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('early-access.services.database.table_name'));

        parent::__construct($attributes);
    }

    /**
     * Adds a new subscriber to the repository.
     *
     * @param  string $email
     * @param  string|null $name
     * @return bool
     */
    public function addSubscriber(string $email, ?string $name): bool
    {
        return (bool) static::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'subscribed_at' => now()]
        );
    }

    /**
     * Removes a subscriber.
     *
     * @param string $email
     * @return bool
     */
    public function removeSubscriber(string $email): bool
    {
        return with(static::byEmail($email)->first(), function (?self $subscriber) {
            return $subscriber ? $subscriber->delete() : false;
        });
    }

    /**
     * Verify the subscriber.
     *
     * @param  string $email
     * @return bool
     */
    public function verify(string $email): bool
    {
        return with(static::byEmail($email)->first(), function (?self $subscriber) {
            return $subscriber ? $subscriber->update(['verified_at' => now()]) : false;
        });
    }

    /**
     * Find subscriber by email.
     *
     * @param  string $email
     * @return array|false
     */
    public function findByEmail(string $email)
    {
        return with(static::byEmail($email)->first(), function (?self $subscriber) {
            return $subscriber ? $subscriber->toArray() : false;
        });
    }

    /**
     * Query by email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
}
