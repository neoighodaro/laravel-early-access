<?php

namespace Neo\EarlyAccess\Tests;

use Closure;
use Mockery;
use Neo\EarlyAccess\Subscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Orchestra\Testbench\TestCase as Orchestra;
use Neo\EarlyAccess\EarlyAccessServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @var \Mockery\MockInterface */
    protected $mock;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        Event::fake();

        Notification::fake();

        $this->resetTestingStage();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->resetTestingStage();

        parent::tearDown();
    }

    /**
     * @param $abstract
     * @param \Closure|null $mock
     * @return \Mockery\MockInterface
     */
    protected function mock($abstract, ?Closure $mock = null)
    {
        $this->mock = Mockery::mock($abstract);

        $this->app->instance($abstract, $this->mock);

        return $this->mock;
    }

    /**
     * @param string|null $name
     * @param string $email
     * @param bool $verified
     * @return \Neo\EarlyAccess\Subscriber
     */
    protected function createSubscriberInstance(string $name = null, string $email = null, bool $verified = true)
    {
        return Subscriber::make([
            'name' => $name ?? 'John Doe',
            'email' => $email ?? 'john@doe.com',
            'subscribed_at' => (string) now(),
            'verified' => $verified,
        ]);
    }

    /**
     * Activates early access.
     */
    protected function activateEarlyAccessConfiguration()
    {
        app('config')->set('early-access.enabled', true);
    }

    /**
     * Deactivates early access.
     */
    protected function deactivateEarlyAccessConfiguration()
    {
        app('config')->set('early-access.enabled', false);
    }

    /**
     * Resets the stage so we can carry out tests.
     */
    protected function resetTestingStage()
    {
        $this->deactivateEarlyAccessConfiguration();

        Storage::disk('local')->delete('early-access');
    }

    /**
     * @param $uri
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function get($uri, array $headers = [])
    {
        return parent::get($uri, $headers);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            EarlyAccessServiceProvider::class,
        ];
    }
}
