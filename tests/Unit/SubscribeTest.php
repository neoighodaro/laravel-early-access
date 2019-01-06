<?php

namespace Neo\EarlyAccess\Tests\Unit;

use Mockery;
use Neo\EarlyAccess\Subscriber;
use Neo\EarlyAccess\Tests\TestCase;
use Neo\EarlyAccess\Contracts\Subscription\SubscriptionProvider;

class SubscribeTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->mock(SubscriptionProvider::class);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function can_subscribe_and_unsubscribe_to_provider_using_email()
    {
        $subscriber = Subscriber::make(['email' => 'john@doe.com']);

        $this->mock->shouldReceive(['add' => true, 'remove' => true])->once()->andReturnTrue();

        $this->assertTrue($subscriber->subscribe());

        $this->assertTrue($subscriber->unsubscribe());
    }

    /** @test */
    public function can_verify_email_address()
    {
        $this->mock->shouldReceive('verify')->once()->andReturnTrue();

        $this->assertTrue(Subscriber::make(['email' => 'neo@ck.co'])->verify());
    }

    /** @test */
    public function can_find_subscriber_by_email_address()
    {
        $subscriber = $this->createSubscriberInstance('John', 'john@doe.com');

        $this->mock->shouldReceive('findByEmail')->with('john@doe.com')->once()->andReturn($subscriber);

        $this->assertEquals($subscriber, $subscriber->findByEmail());
    }
}
