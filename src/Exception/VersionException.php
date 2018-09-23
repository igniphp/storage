<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class VersionException extends StorageException
{
    public static function forParseError(string $version): self
    {
        return new self("String `{$version}` could not be recognized as valid semantic version.");
    }
}
