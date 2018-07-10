<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

interface MemorySavingHydrator extends ObjectHydrator
{
    public function saveMemory(bool $save = true): void;
}
