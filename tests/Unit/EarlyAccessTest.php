<?php

namespace Neo\EarlyAccess\Tests\Unit;

use Neo\EarlyAccess\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Neo\EarlyAccess\Traits\InteractsWithEarlyAccess;

class EarlyAccessTest extends TestCase
{
    use InteractsWithEarlyAccess;

    /** @test */
    public function shows_if_early_access_is_enabled_or_not()
    {
        $this->activateEarlyAccessConfiguration();

        $this->assertTrue($this->isEarlyAccessEnabled());

        $this->deactivateEarlyAccessConfiguration();

        $this->assertFalse($this->isEarlyAccessEnabled());
    }

    /** @test */
    public function can_save_and_delete_beacon_file()
    {
        $this->saveBeacon();

        $this->assertTrue(Storage::disk('local')->exists('early-access'));

        $this->deleteBeacon();

        $this->assertFalse(Storage::disk('local')->exists('early-access'));
    }

    /** @test */
    public function can_save_a_list_of_networks_to_beacon_file()
    {
        $this->saveBeacon(['127.0.0.1']);

        $this->addAllowedNetworksToBeacon(['0.0.0.0', '1.1.1.1']);

        $this->assertArraySubset(['127.0.0.1', '0.0.0.0', '1.1.1.1'], $this->getAllowedNetworks());
    }

    /** @test */
    public function can_get_beacon_details()
    {
        $this->assertFalse($this->getBeaconDetails());

        $this->saveBeacon(['127.0.0.1']);

        $details = $this->getBeaconDetails();

        $this->assertArrayHasKey('time', $details);

        $this->assertArraySubset(['allowed' => ['127.0.0.1']], $details);
    }

    /** @test */
    public function adding_multiple_allowed_appends_to_allowed_list()
    {
        $this->saveBeacon(['127.0.0.1']);

        $this->saveBeacon(['1.1.1.1']);

        $this->assertArraySubset(['127.0.0.1', '1.1.1.1'], $this->getAllowedNetworks());
    }

    /** @test */
    public function removes_duplicate_networks_from_list()
    {
        $this->saveBeacon(['127.0.0.1', '0.0.0.0', '0.0.0.0']);

        $this->saveBeacon(['1.1.1.1']);

        $this->saveBeacon(['1.1.1.1']);

        $this->assertArraySubset(['127.0.0.1', '0.0.0.0', '1.1.1.1'], $this->getAllowedNetworks());
    }
}
