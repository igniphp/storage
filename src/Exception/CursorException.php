<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Storage\Driver\Cursor;

class CursorException extends DriverException
{
    public static function forEmptyResult(): self
    {
        return new self('Could not fetch empty result.');
    }

    public static function forExecutionFailure(Cursor $cursor, string $reason): self
    {
        $cursor = get_class($cursor);

        return new self("Could not execute cursor ${cursor}, reason: ${reason}");
    }

    public static function forDetachedHydrator(): self
    {
        return new self('This cursor has no hydrator attached to it.');
    }
}
