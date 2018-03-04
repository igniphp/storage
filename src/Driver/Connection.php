<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

interface Connection
{
    public function close(): void;
    public function open(): void;
    public function isOpen(): bool;
    public function execute(...$parameters);
    public function getBaseConnection();
}
