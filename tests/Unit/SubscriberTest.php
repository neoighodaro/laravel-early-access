<?php

namespace Neo\EarlyAccess\Tests\Unit;

use Neo\EarlyAccess\Subscriber;
use Neo\EarlyAccess\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Neo\EarlyAccess\Events\UserSubscribed;
use Illuminate\Support\Facades\Notification;
use Neo\EarlyAccess\Events\UserUnsubscribed;
use Neo\EarlyAccess\Contracts\Subscription\SubscriptionProvider;
use Neo\EarlyAccess\Notifications\UserSubscribed as UserSubscribedNotification;
use Neo\EarlyAccess\Notifications\UserUnsubscribed as UserUnsubscribedNotification;

class SubscriberTest extends TestCase
{
    /** @var \Neo\EarlyAccess\Subscriber $subscriber */
    private $subscriber;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(SubscriptionProvider::class);

        $this->subscriber = $this->createSubscriberInstance();
    }

    /** @test */
    public function can_get_subscriber_details()
    {
        $this->assertEquals('John Doe', $this->subscriber->name);

        $this->assertEquals('john@doe.com', $this->subscriber->email);

        $this->assertIsString($this->subscriber->subscribed_at);

        $this->assertTrue($this->subscriber->verified);
    }

    /** @test */
    public function can_subscribe_and_unsubscribe_from_subscriber_instance()
    {
        $this->mock->shouldReceive(['add' => true, 'remove' => true]);

        $this->assertTrue($this->subscriber->subscribe());

        $this->assertTrue($this->subscriber->subscribed());

        $this->assertTrue($this->subscriber->unsubscribe());

        $this->assertFalse($this->subscriber->subscribed());
    }

    /** @test */
    public function event_is_fired_on_subscribe()
    {
        $this->mock->shouldReceive(['add' => true]);

        $this->subscriber->subscribe();

        Event::assertDispatched(UserSubscribed::class, function ($e) {
            return $e->subscriber->email === $this->subscriber->email;
        });
    }

    /** @test */
    public function event_is_fired_on_unsubscribe()
    {
        $this->mock->shouldReceive(['remove' => true]);

        $this->subscriber->setSubscribed()->unsubscribe();

        Event::assertDispatched(UserUnsubscribed::class, function ($e) {
            return $e->subscriber->email === $this->subscriber->email;
        });
    }

    /** @test */
    public function can_set_subscribed_status()
    {
        $this->mock->shouldReceive(['add' => true, 'remove' => true]);

        $this->subscriber->subscribe();

        $this->assertTrue($this->subscriber->subscribed());

        $this->subscriber->setSubscribed(false);

        $this->assertFalse($this->subscriber->subscribed());
    }

    /** @test */
    public function can_find_by_email()
    {
        //
        // Find user by supplying email using empty Subscriber object
        //

        $this->mock->shouldReceive('findByEmail')
            ->with($this->subscriber->email)
            ->once()
            ->andReturn($this->subscriber);

        $this->assertEquals($this->subscriber, Subscriber::make()->findByEmail($this->subscriber->email));

        //
        // Find user by not supplying email using empty Subscriber object
        //

        $this->mock->shouldReceive('findByEmail')->times(0);

        $this->assertNull(Subscriber::make()->findByEmail());

        //
        // Find user by supplying email using loaded Subscriber object
        //

        $this->mock->shouldReceive('findByEmail')
            ->with($this->subscriber->email)
            ->times(2)
            ->andReturn($this->subscriber);

        $subscriber = $this->createSubscriberInstance('Neo Ighodaro', 'neo@ck.co', true);

        $subscriber->setSubscribed(false);

        $this->assertEquals($this->subscriber, $subscriber->findByEmail($this->subscriber->email));

        $subscriber->setSubscribed(true);

        $this->assertEquals($this->subscriber, $subscriber->findByEmail($this->subscriber->email));
    }

    /** @test */
    public function can_use_local_instance_to_find_email_if_existing()
    {
        $this->subscriber->setSubscribed();

        $this->mock->shouldReceive('findByEmail')->times(0);

        $this->assertEquals($this->subscriber, $this->subscriber->findByEmail());

        $this->subscriber->setSubscribed(false);

        $this->mock->shouldReceive('findByEmail')->once()->andReturn($this->subscriber);

        $this->assertEquals($this->subscriber, $this->subscriber->findByEmail());
    }

    /** @test */
    public function can_verify_subscriber()
    {
        $this->assertFalse($this->subscriber->subscribed());

        $this->mock->shouldReceive('verify')->once()->with($this->subscriber->email)->andReturn(true);

        $this->subscriber->verify();

        $this->assertTrue($this->subscriber->verified);

        $this->assertTrue($this->subscriber->subscribed());
    }

    /** @test */
    public function notifications_can_be_sent_from_subscriber_instance()
    {
        $this->subscriber->notify(new UserSubscribedNotification($this->subscriber));

        Notification::assertSentTo(
            $this->subscriber,
            UserSubscribedNotification::class,
            function ($notification) {
                return $notification->subscriber->email === $this->subscriber->email;
            }
        );

        $this->subscriber->notify(new UserUnsubscribedNotification($this->subscriber));

        Notification::assertSentTo(
            $this->subscriber,
            UserUnsubscribedNotification::class,
            function ($notification) {
                return $notification->subscriber->email === $this->subscriber->email;
            }
        );
    }
}
