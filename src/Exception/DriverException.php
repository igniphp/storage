<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class DriverException extends StorageException
{
    public static function forOperationFailure(string $message): self
    {
        return new self($message);
    }

    public static function forUnsupportedDriver(): self
    {
        return new self("Unsupported driver.");
    }
}
