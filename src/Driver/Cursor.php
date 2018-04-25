<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

interface Cursor extends \Iterator
{
    public function close(): void;
    public function open(): void;
    public function getConnection(): Connection;
}
