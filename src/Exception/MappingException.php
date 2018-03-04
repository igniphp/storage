<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class MappingException extends StorageException
{
    public static function forUnknownMappingStrategy(string $type): MappingException
    {
        return new self("Unknown mapping strategy - `${type}`. Did you forgot to call Strategy::register()?");
    }

    public static function forNonRegisteredSchema(string $entity): MappingException
    {
        return new self("Entity `${entity}` has no schema assigned.");
    }
}
