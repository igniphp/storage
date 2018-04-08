<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\Hydration\HydratorGenerator\GeneratedHydrator;

interface HydratorGenerator
{
    public function generate(string $entityClass): GeneratedHydrator;
}
