<?php

namespace Webnuvola\Laravel\ServerEvents\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Webnuvola\Laravel\ServerEvents\ServerEventsServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'array');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServerEventsServiceProvider::class];
    }
}
