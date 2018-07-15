<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class MappingException extends StorageException
{
    public static function forUnknownMappingStrategy(string $type): self
    {
        return new self("Unknown mapping strategy - `{$type}`. Did you forgot to call Strategy::register()?");
    }

    public static function forEmptyMapping(string $class): self
    {
        return new self("Passed entity {$class} defines no mapped properties. Are you sure you have passed right class?");
    }

    public static function forNonRegisteredSchema(string $entity): self
    {
        return new self("Entity `{$entity}` has no schema assigned");
    }

    public static function forInvalidUuid($value): self
    {
        return new self("Passed value({$value}) is not valid uuid string");
    }

    public static function forInvalidAttributeValue(string $name, $value, string $message = ''): self
    {
        $dump = var_export($value, true);
        return new self("Invalid value ({$dump}) for attribute `{$name}`. {$message}");
    }

    public static function forInvalidEntityClass(string $name): self
    {
        return new self("Entity class `$name` was not found. Are you sure it was add to autoload?");
    }
}
