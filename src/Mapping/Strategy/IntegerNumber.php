<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class IntegerNumber implements MappingStrategy
{
    public function hydrate($value): float
    {
        return (int) $value;
    }

    public function extract($value): float
    {
        return (int) $value;
    }
}
