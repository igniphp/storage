<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Entity;
use Igni\Storage\Hydration\HydratorGenerator\GeneratedHydrator;
use Igni\Storage\Mapping\MappingContext;
use Igni\Storage\Mapping\MappingStrategy;

/**
 * @see GeneratedHydrator
 */
final class Reference implements MappingStrategy
{
    public static function hydrate($value, MappingContext $context, array $options = [])
    {
        return $context->getEntityManager()->get($context->getEntityClass(), $value);
    }

    public static function extract($value, MappingContext $context, array $options = [])
    {
        if ($value instanceof Entity) {
            return $value->getId() ? $value->getId()->getValue() : null;
        }
    }
}
