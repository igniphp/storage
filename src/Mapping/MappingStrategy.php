<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

interface MappingStrategy
{
    public function hydrate($value);
    public function extract($value);
}
