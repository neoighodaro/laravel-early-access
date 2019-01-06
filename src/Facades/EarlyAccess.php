<?php

namespace Neo\EarlyAccess\Facades;

use Illuminate\Support\Facades\Facade;

class EarlyAccess extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'early-access';
    }
}
