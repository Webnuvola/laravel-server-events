<?php

namespace Webnuvola\Laravel\ServerEvents\Test;

use Illuminate\Cache\Repository;
use Illuminate\Support\Str;
use Webnuvola\Laravel\ServerEvents\Dispatcher;
use Webnuvola\Laravel\ServerEvents\Events\FacebookServerEvent;
use Webnuvola\Laravel\ServerEvents\ServerEvent;

class ServerEventTest extends TestCase
{
    protected Dispatcher $dispatcher;
    protected Repository $cache;

    public function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = app('server-events');
        $this->cache = app('cache')->driver();
    }

    /** @test */
    public function create_server_event()
    {
        $serverEvent = new FacebookServerEvent([]);

        self::assertInstanceOf(ServerEvent::class, $serverEvent);
        self::assertNotEmpty($serverEvent->getId());
    }

    /** @test */
    public function assign_and_read_id()
    {
        $uuid = Str::uuid()->toString();
        $serverEvent = new FacebookServerEvent([], $uuid);

        self::assertSame($uuid, $serverEvent->getId());

        $uuid = Str::uuid()->toString();
        $serverEvent = new FacebookServerEvent([], 'abcd');
        $serverEvent->setId($uuid);

        self::assertSame($uuid, $serverEvent->getId());
    }

    /** @test */
    public function assign_and_read_data()
    {
        $data = [
            'action' => 'ViewContent',
            'content_ids' => [125],
            'content_name' => 'Product Name',
            'content_type' => 'product',
            'currency' => 'EUR',
            'value' => 53.28,
            'mpn' => '12000AB',
        ];

        $serverEvent = new FacebookServerEvent($data);

        self::assertSame($data, $serverEvent->getData());

        $serverEvent = new FacebookServerEvent([]);
        $serverEvent->setData($data);

        self::assertSame($data, $serverEvent->getData());
    }

    /** @test */
    public function json_conversion()
    {
        $uuid = Str::uuid();
        $data = [
            'action' => 'ViewContent',
            'content_ids' => [125],
            'content_name' => 'Product Name',
            'content_type' => 'product',
            'currency' => 'EUR',
            'value' => 53.28,
            'mpn' => '12000AB',
        ];

        $serverEvent = new FacebookServerEvent($data, $uuid);
        $json = $serverEvent->toJson(JSON_THROW_ON_ERROR);

        self::assertJson($json);
        self::assertJsonStringEqualsJsonString(json_encode([
            'id' => $uuid,
            'data' => $data,
            'class' => FacebookServerEvent::class,
        ], JSON_THROW_ON_ERROR), $json);
    }

    /** @test */
    public function save_and_retrieve_from_cache()
    {
        $uuid = Str::uuid();
        $data = [
            'action' => 'ViewContent',
            'content_ids' => [125],
            'content_name' => 'Product Name',
            'content_type' => 'product',
            'currency' => 'EUR',
            'value' => 53.28,
            'mpn' => '12000AB',
        ];

        $serverEvent = new FacebookServerEvent($data, $uuid);
        $serverEvent->save();

        $cacheKey = $this->dispatcher->cacheKey($uuid);
        self::assertJsonStringEqualsJsonString($serverEvent->toJson(), $this->cache->get($cacheKey));

        $serverEventFromCache = $this->dispatcher->find($uuid);
        self::assertJsonStringEqualsJsonString($serverEvent->toJson(), $serverEventFromCache->toJson());

        $this->dispatcher->delete($serverEvent);
        self::assertNull($this->cache->get($cacheKey));
    }
}
