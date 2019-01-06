<?php

namespace Neo\EarlyAccess;

use Illuminate\Support\InteractsWithTime;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Contracts\Filesystem\Filesystem as Storage;

class EarlyAccess
{
    use InteractsWithTime;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $storage;
    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    private $auth;

    /**
     * EarlyAccess constructor.
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $storage
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Storage $storage, Auth $auth)
    {
        $this->auth = $auth;

        $this->storage = $storage;
    }

    /**
     * Checks if early access is enabled or not.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        if ($this->auth->user()) {
            return false;
        }

        return (bool) $this->getBeaconDetails() ?: config('early-access.enabled');
    }

    /**
     * Returns a list of allowed networks.
     *
     * @return array
     */
    public function allowedNetworks(): array
    {
        $data = $this->getBeaconDetails() ?? [];

        return array_get($data, 'allowed', []);
    }

    /**
     * Adds a network to the list of allowed networks.
     *
     * @param array $networks
     */
    public function addAllowedNetworksToBeacon(array $networks)
    {
        if ($data = $this->getBeaconDetails()) {
            array_push($data['allows'], ...$networks);

            $data['allowed'] = array_unique($data['allowed']);

            return $this->saveBeaconFileWithData($data);
        }
    }

    /**
     * Save the beacon file.
     *
     * @param array $allowed
     * @return bool
     */
    public function saveBeacon(array $allowed = []): bool
    {
        if ($this->getBeaconDetails()) {
            return (bool) $this->addAllowedNetworksToBeacon($allowed);
        }

        return $this->saveBeaconFileWithData([
            'allowed' => array_unique($allowed),
            'time' => $this->currentTime(),
        ]);
    }

    /**
     * Deletes the beacon file.
     *
     * @return bool
     */
    public function deleteBeacon(): bool
    {
        return $this->storage->delete('early-access');
    }

    /**
     * Get the beacon file details.
     *
     * @return false|array
     */
    public function getBeaconDetails()
    {
        if (!$this->storage->exists('early-access')) {
            return false;
        }

        return json_decode($this->storage->get('early-access'), true);
    }

    /**
     * Saves the beacon file.
     *
     * @param array $data
     * @return bool
     */
    private function saveBeaconFileWithData(array $data): bool
    {
        return $this->storage->put('early-access', json_encode($data, JSON_PRETTY_PRINT));
    }
}
