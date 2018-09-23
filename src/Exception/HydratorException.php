<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Storage\Hydration\ObjectHydrator;

class HydratorException extends StorageException
{
    public static function forNonRegisteredHydrator(string $entityClass): self
    {
        return new self("Hydrator for entity ${entityClass} was not registered.");
    }

    public static function forUndefinedSchema(string $entity): self
    {
        return new self("Entity ${entity} has no schema attached. Please define schema first.");
    }

    public static function forNotSetStrategyArgument(string $argument): self
    {
        return new self("Strategy defines no value for argument ${argument}.");
    }

    public static function forMissingHydrateMethod(ObjectHydrator $hydrator, string $property): self
    {
        $class = get_class($hydrator);
        return new self("Hydrator ${class} is missing hydration method for property ${property}.");
    }

    public static function forMissingExtractMethod(ObjectHydrator $hydrator, string $property): self
    {
        $class = get_class($hydrator);
        return new self("Hydrator ${class} is missing extraction method for property ${property}.");
    }

    public static function forInvalidNamingStrategy(ObjectHydrator $hydrator): self
    {
        $class = get_class($hydrator);
        return new self("Hydrator ${class} provides invalid naming strategy.");
    }

    public static function forMissingIdentity(string $entityClass): self
    {
        return new self("Cannot hydrate ${entityClass} its schema is missing valid identity.");
    }

    public static function forMissingProperty(string $entityClass, string $property): self
    {
        return new self("Trying to hydrate property `${property}`, seems like it does not exists in entity `${entityClass}`");
    }
}
