<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use RuntimeException;

class CollectionException extends RuntimeException
{
    public static function forInvalidIndex(int $index): self
    {
        return new self("Invalid index `$index` passed. Index must be positive number within the maximum collection length n + 1.");
    }

    public static function forOutOfBoundsIndex(int $index): self
    {
        return new self("Index `$index` is out of bounds.");
    }
}
