<?php

namespace Webnuvola\Laravel\ServerEvents;

use Illuminate\Contracts\Bus\Dispatcher as LaravelDispatcher;
use Illuminate\Contracts\Cache\Repository;
use Webnuvola\Laravel\ServerEvents\Contracts\ServerEvent;

class Dispatcher
{
    protected LaravelDispatcher $eventDispatcher;
    protected Repository $cacheStorage;
    protected int $cacheTtl = 3600;

    public function __construct(LaravelDispatcher $eventDispatcher, Repository $cacheStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->cacheStorage = $cacheStorage;
    }

    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    public function setCacheTtl(int $cacheTtl): void
    {
        $this->cacheTtl = $cacheTtl;
    }

    public function save(ServerEvent $event): void
    {
        $this->cacheStorage->put(
            $this->cacheKey($event->getId()),
            $event->toJson(JSON_THROW_ON_ERROR),
            $this->cacheTtl,
        );
    }

    public function find(string $id): ?ServerEvent
    {
        $eventData = $this->cacheStorage->get($this->cacheKey($id));

        if (! $eventData) {
            return null;
        }

        $data = json_decode($eventData, true, 512, JSON_THROW_ON_ERROR);

        $class = $data['class'];

        if (! class_exists($class)) {
            return null;
        }

        if (! in_array(ServerEvent::class, class_implements($class), true)) {
            return null;
        }

        return new $class($data['data'], $data['id']);
    }

    public function delete(ServerEvent $event): void
    {
        $this->cacheStorage->delete($this->cacheKey($event->getId()));
    }

    public function cacheKey(string $id): string
    {
        return 'webnuvola.laravel-server-events.' . $id;
    }
}
