<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Storage\Driver\Cursor;

class CursorException extends DriverException
{
    public static function forEmptyResult(): CursorException
    {
        return new self('Could not fetch empty result.');
    }

    public static function forExecutionFailure(Cursor $cursor, string $reason): CursorException
    {
        $cursor = get_class($cursor);

        return new self("Could not execute cursor ${cursor}, reason: ${reason}");
    }
}
