<?php

namespace Webnuvola\Laravel\ServerEvents\Test;

use Webnuvola\Laravel\ServerEvents\Dispatcher;

class DispatcherTest extends TestCase
{
    protected Dispatcher $dispatcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = app('server-events');
    }

    /** @test */
    public function set_cache_ttl()
    {
        $cacheTtl = 1234;

        $this->dispatcher->setCacheTtl($cacheTtl);

        self::assertSame($cacheTtl, $this->dispatcher->getCacheTtl());
    }

    /** @test */
    public function no_server_event_found()
    {
        self::assertNull($this->dispatcher->find('id-not-present'));
    }

    /** @test */
    public function cache_key()
    {
        self::assertIsString($this->dispatcher->cacheKey('id-not-present'));
    }
}
