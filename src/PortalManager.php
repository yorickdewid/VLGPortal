<?php

namespace VLG\GSSAuth;

use InvalidArgumentException;
use Illuminate\Support\Manager;

class PortalManager extends Manager implements Contracts\Factory
{
    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \VLG\GSSAuth\Two\AbstractProvider
     */
    protected function createVLGPortalDriver()
    {
        $config = $this->app['config']['services.vlgportal'];

        return $this->buildProvider('VLG\GSSAuth\VLGPortalProvider', $config);
    }

    /**
     * Build a provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \VLG\GSSAuth\Two\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        $host = null;
        if (isset($config['host'])) {
            $host = $config['host'];
        }

        $options = array_except($config, ['key', 'secret', 'host']);
        return new $provider(
            $this->app['request'],
            $config['key'],
            $config['secret'],
            $host,
            $options
        );
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No driver was specified.');
    }
}
