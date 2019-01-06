<?php

namespace Neo\EarlyAccess\Traits;

use Neo\EarlyAccess\Facades\EarlyAccess;

trait InteractsWithEarlyAccess
{
    /**
     * @return bool
     */
    public function isEarlyAccessEnabled(): bool
    {
        return EarlyAccess::isEnabled();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function saveBeacon(array $data = [])
    {
        return EarlyAccess::saveBeacon($data);
    }

    /**
     * @return mixed
     */
    public function deleteBeacon()
    {
        return EarlyAccess::deleteBeacon();
    }

    /**
     * @return mixed
     */
    public function getBeaconDetails()
    {
        return EarlyAccess::getBeaconDetails();
    }

    /**
     * @return mixed
     */
    public function getAllowedNetworks()
    {
        return EarlyAccess::allowedNetworks();
    }

    /**
     * @param array $networks
     * @return mixed
     */
    public function addAllowedNetworksToBeacon(array $networks)
    {
        return EarlyAccess::addAllowedNetworksToBeacon($networks);
    }
}
