<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class SchemaException extends MappingException
{
    public static function forMissingSchema(string $entity): self
    {
        return new self("There is no schema defined for ${entity}.");
    }

    public static function forSchemaMutation(): self
    {
        return new self('Schema is read only.');
    }

    public static function forEmptySchemaDefinition(): self
    {
        return new self('Schema defines no properties, have you forgot to use Schema::property() method.');
    }

    public static function forUndefinedSource(): self
    {
        return new self('Schema defines no source, thus entity can be persisted only as embed entity.');
    }
}
