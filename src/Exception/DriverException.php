<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class DriverException extends StorageException
{
    public static function forOperationFailure(string $message): DriverException
    {
        return new self($message);
    }
}
