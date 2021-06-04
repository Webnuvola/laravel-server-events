<?php

namespace Webnuvola\Laravel\ServerEvents\Contracts;

use Illuminate\Contracts\Support\Jsonable;

interface ServerEvent extends Jsonable
{
    public function getId(): string;
    public function setId(string $id): void;
    public function save(): void;
}
