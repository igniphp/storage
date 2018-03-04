<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Storage\Hydration\Hydrator;

class HydratorException extends StorageException
{
    public static function forNonRegisteredHydrator(string $entityClass): HydratorException
    {
        return new self("Hydrator for entity ${entityClass} was not registered.");
    }

    public static function forUndefinedSchema(string $entity): HydratorException
    {
        return new self("Entity ${entity} has no schema attached. Please define schema first.");
    }

    public static function forNotSetStrategyArgument(string $argument): HydratorException
    {
        return new self("Strategy defines no value for argument ${argument}.");
    }

    public static function forMissingHydrateMethod(Hydrator $hydrator, string $property): HydratorException
    {
        $class = get_class($hydrator);
        return new self("Hydrator ${class} is missing hydration method for property ${property}.");
    }

    public static function forMissingExtractMethod(Hydrator $hydrator, string $property): HydratorException
    {
        $class = get_class($hydrator);
        return new self("Hydrator ${class} is missing extraction method for property ${property}.");
    }

    public static function forInvalidNamingStrategy(Hydrator $hydrator): HydratorException
    {
        $class = get_class($hydrator);
        return new self("Hydrator ${class} provides invalid naming strategy.");
    }

    public static function forMissingIdentity(string $entityClass): HydratorException
    {
        return new self("Cannot hydrate ${entityClass} its schema is missing valid identity.");
    }

    public static function forMissingProperty(string $entityClass, string $property): HydratorException
    {
        return new self("Trying to hydrate property `${property}`, seems like it does not exists in entity `${entityClass}`");
    }
}
