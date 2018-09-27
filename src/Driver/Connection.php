<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

interface Connection
{
    public function close(): void;
    public function connect(): void;
    public function isConnected(): bool;
    public function createCursor(...$parameters);
}
