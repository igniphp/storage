<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Mapping\MappingStrategy;

final class Id implements MappingStrategy
{
    public function hydrate($value)
    {
        return $value;
    }

    public function extract($value)
    {
        return $value;
    }
}
