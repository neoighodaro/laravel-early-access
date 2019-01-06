<?php

namespace Neo\EarlyAccess\Tests\Feature;

use Neo\EarlyAccess\Tests\TestCase;
use Neo\EarlyAccess\Traits\InteractsWithEarlyAccess;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;

class CommandsTest extends TestCase
{
    use InteractsWithConsole, InteractsWithEarlyAccess;

    /** @test */
    public function activates_when_artisan_command_is_called()
    {
        $this->artisan('early-access', ['--activate' => true]);

        $this->assertTrue($this->isEarlyAccessEnabled());
    }

    /** @test */
    public function deactivates_when_artisan_command_is_called()
    {
        $this->artisan('early-access', ['--deactivate' => true]);

        $this->assertFalse($this->isEarlyAccessEnabled());
    }

    /** @test */
    public function adds_current_ip_to_allowed_list()
    {
        $this->artisan('early-access', [
            '--activate' => true,
            '--allow' => ['127.0.0.1', '1.1.1.1'],
        ]);

        $this->assertArraySubset(['127.0.0.1', '1.1.1.1'], $this->getAllowedNetworks());
    }

    /** @test */
    public function can_check_early_access_status()
    {
        $this->artisan('early-access', ['--deactivate' => true]);

        $this->artisan('early-access', ['status' => true])
            ->expectsOutput('Not active');

        $this->artisan('early-access', ['--activate' => true]);

        $this->artisan('early-access', ['status' => true])
            ->expectsOutput('Active. Allowed networks: none');

        $this->artisan('early-access', ['--deactivate' => true]);
        $this->artisan('early-access', ['--allow' => ['127.0.0.1', '1.1.1.1']]);

        $this->artisan('early-access', ['status' => true])
            ->expectsOutput('Active. Allowed networks: 127.0.0.1, 1.1.1.1');
    }
}
