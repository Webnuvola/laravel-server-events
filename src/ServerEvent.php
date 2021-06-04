<?php

namespace Webnuvola\Laravel\ServerEvents;

use Illuminate\Support\Str;
use Webnuvola\Laravel\ServerEvents\Contracts\ServerEvent as ServerEventContract;

abstract class ServerEvent implements ServerEventContract
{
    protected string $id;
    protected array $data = [];

    public function __construct(array $data, string $id = null)
    {
        $this->id = $id ?: Str::uuid();
        $this->data = $data;
    }

    public static function find(string $id): ?ServerEvent
    {
        return app(Dispatcher::class)->find($id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function save(): void
    {
        app(Dispatcher::class)->save($this);
    }

    public function toJson($options = 0)
    {
        return json_encode([
            'id' => $this->id,
            'data' => $this->data,
            'class' => static::class,
        ], $options);
    }
}
