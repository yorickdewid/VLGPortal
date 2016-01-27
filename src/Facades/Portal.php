<?php

namespace VLG\GSSAuth\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \VLG\GSSAuth\SocialiteManager
 */
class Portal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'VLG\GSSAuth\Contracts\Factory';
    }
}
