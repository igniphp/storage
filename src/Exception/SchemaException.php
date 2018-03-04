<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class SchemaException extends MappingException
{
    public static function forInvalidEntityClass(string $type): SchemaException
    {
        return new self("Cannot initialize schema for the class ${type}. ${type} must be instance of Entity class.");
    }

    public static function forMissingSchema(string $entity): SchemaException
    {
        return new self("There is no schema defined for ${entity}.");
    }

    public static function forSchemaMutation(): SchemaException
    {
        return new self('Schema is read only.');
    }

    public static function forEmptySchemaDefinition(): SchemaException
    {
        return new self('Schema defines no properties, have you forgot to use Schema::property() method.');
    }

    public static function forUndefinedSource(): SchemaException
    {
        return new self('Schema defines no source, thus entity can be persisted only as embed entity.');
    }
}
