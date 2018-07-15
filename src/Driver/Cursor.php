<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

interface Cursor extends \Iterator
{
    public function open(): void;
    public function close(): void;
    public function key(): int;
    public function getConnection(): Connection;
}
