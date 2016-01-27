<?php

namespace VLG\GSSAuth\Contracts;

interface Factory
{
    /**
     * Get the provider implementation.
     *
     * @param  string  $driver
     * @return \VLG\GSSAuth\Contracts\Provider
     */
    public function driver($driver = null);
}
