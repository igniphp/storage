<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

use Igni\Storage\Hydration\ObjectHydrator;

interface HydratingCursor extends Cursor
{
    public function hydrateWith(ObjectHydrator $hydrator): void;
}
